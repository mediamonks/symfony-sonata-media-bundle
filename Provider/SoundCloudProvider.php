<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Entity\Media;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Form\Type\ImmutableArrayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SoundCloudProvider extends AbstractOembedProvider implements ProviderInterface
{
    const URL_OEMBED = 'https://soundcloud.com/oembed?format=json&url=https://soundcloud.com/%s';
    const URL = 'https://soundcloud.com/%s';

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderEditFormBefore(FormMapper $formMapper)
    {
        $formMapper->add('providerReference', TextType::class, ['label' => $this->getReferenceLabel()]);
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderEditFormAfter(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Embed Options')
            ->add(
                'providerMetaData',
                ImmutableArrayType::class,
                [
                    'keys' => [
                        ['autoPlay', CheckboxType::class, ['label' => 'form.auto_play', 'required' => false]],
                        ['hideRelated', CheckboxType::class, ['label' => 'form.hide_related', 'required' => false]],
                        ['showComments', CheckboxType::class, ['label' => 'form.show_comments', 'required' => false]],
                        ['showUser', CheckboxType::class, ['label' => 'form.show_user', 'required' => false]],
                        ['showReposts', CheckboxType::class, ['label' => 'form.show_reposts', 'required' => false]],
                        ['showVisual', CheckboxType::class, ['label' => 'form.show_visual', 'required' => false]],
                    ],
                    'label' => 'form.embed_options',
                    'required' => false
                ]
            )->end()
        ;
    }

    /**
     * @param string $id
     * @return string
     */
    public function getOembedUrl($id)
    {
        return sprintf(self::URL_OEMBED, $id);
    }

    /**
     * @param $value
     * @return string
     * @throws \Exception
     */
    public function parseProviderReference($value)
    {
        if (strpos($value, 'soundcloud.com')) {
            $url = parse_url($value);

            return trim($url['path'], '/');
        }

        return $value;
    }

    /**
     * @param $id
     * @return array
     */
    protected function getOembedData($id)
    {
        $data = parent::getOembedData($id);
        $data['embedUrl'] = $this->extractEmbedUrl($data);

        return $data;
    }

    /**
     * @param array $data
     */
    protected function extractEmbedUrl(array $data)
    {
        preg_match('/src="(.*)"/', $data['html'], $matches);
        $url = $matches[1];

        $data = parse_url($url);
        parse_str($data['query'], $data);

        return $data['url'];
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return 'fa fa-soundcloud';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'soundcloud';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return AbstractProvider::TYPE_AUDIO;
    }
}
