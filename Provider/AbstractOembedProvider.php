<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Model\AbstractMedia;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractOembedProvider extends AbstractProvider implements OembedProviderInterface
{
    /**
     * @var array
     */
    protected $oembedData;

    /**
     * @param AbstractMedia $media
     * @param bool $providerReferenceUpdated
     * @throws \Exception
     */
    public function update(AbstractMedia $media, $providerReferenceUpdated)
    {
        if ($providerReferenceUpdated) {
            $media->setProviderReference($this->parseProviderReference($media->getProviderReference()));

            $data = $this->getOembedData($media->getProviderReference());

            $media->setProviderMetaData($data);

            if (empty($media->getTitle()) && isset($data['title'])) {
                $media->setTitle($data['title']);
            }
            if (empty($media->getDescription()) && isset($data['description'])) {
                $media->setDescription($data['description']);
            }
            if (empty($media->getAuthorName()) && isset($data['author_name'])) {
                $media->setAuthorName($data['author_name']);
            }
            if (empty($media->getImage())) {
                $this->refreshImage($media);
            }
        }

        parent::update($media, $providerReferenceUpdated);
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderCreateForm(FormMapper $formMapper)
    {
        $formMapper->add(
            'providerReference',
            TextType::class,
            ['label' => $this->getReferenceLabel()]
        );
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderEditFormBefore(FormMapper $formMapper)
    {
        $formMapper->add(
            'providerReference',
            TextType::class,
            ['label' => $this->getReferenceLabel()]
        );
    }

    /**
     * @param ErrorElement $errorElement
     * @param AbstractMedia $media
     */
    public function validate(ErrorElement $errorElement, AbstractMedia $media)
    {
        try {
            $this->getOembedData($this->parseProviderReference($media->getProviderReference()));
        }
        catch (\Exception $e) {
            $errorElement->with('providerReference')->addViolation($e->getMessage());
        }
    }

    /**
     * @param \MediaMonks\SonataMediaBundle\Model\AbstractMedia $media
     */
    public function refreshImage(AbstractMedia $media)
    {
        $filename = sprintf('%s_%d.%s', sha1($media->getProviderReference()), time(), 'jpg');
        $thumbnailUrl = $this->getImageUrl($media->getProviderReference());

        $this->getFilesystem()->write(
            $filename,
            file_get_contents($thumbnailUrl)
        );
        $media->setImage($filename);
    }

    /**
     * @param string $id
     * @return string
     */
    public function getImageUrl($id)
    {
        return $this->getOembedData($id)['thumbnail_url'];
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    protected function getOembedData($id)
    {
        if (empty($this->oembedData)) {

            $this->disableErrorHandler();
            $data = json_decode($this->getUrlData($this->getOembedUrl($id)), true);
            $this->restoreErrorHandler();

            if (empty($data['title'])) {
                throw new \Exception($this->getTranslator()->trans('error.provider_reference', [
                    '%provider%' => $this->getName(),
                    '%reference%' => $id
                ]));
            }

            $this->oembedData = $data;
        }

        return $this->oembedData;
    }

    /**
     * @return string
     */
    public function getReferenceLabel()
    {
        return sprintf('form.%s.reference', $this->getName());
    }

    /**
     * @return bool
     */
    public function supportsDownload()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function supportsEmbed()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function supportsImage()
    {
        return true;
    }

    /**
     * @param $url
     * @return mixed
     */
    protected function getUrlData($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    /**
     * @param string $url
     * @return bool
     */
    protected function urlExists($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');

        curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return $info === Response::HTTP_OK;
    }
}
