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
    public function buildProviderEditForm(FormMapper $formMapper)
    {
        $formMapper->add('providerReference', TextType::class, ['label' => 'SoundCloud URL']);

        $formMapper->add(
            'providerMetaData',
            ImmutableArrayType::class,
            [
                'keys' => [
                    ['autoPlay', CheckboxType::class, ['label' => 'Auto Play', 'required' => false]],
                    ['hideRelated', CheckboxType::class, ['label' => 'Hide Related', 'required' => false]],
                    ['showComments', CheckboxType::class, ['label' => 'Show Comments', 'required' => false]],
                    ['showUser', CheckboxType::class, ['label' => 'Show User', 'required' => false]],
                    ['showReposts', CheckboxType::class, ['label' => 'Show Reposts', 'required' => false]],
                    ['showVisual', CheckboxType::class, ['label' => 'Show Visual', 'required' => false]],
                ],
                'label' => 'Embed Options',
                'required' => false
            ]
        );
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
    public function getTitle()
    {
        return 'SoundCloud';
    }

    public function getType()
    {
        return 'soundcloud';
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return AbstractProvider::CATEGORY_AUDIO;
    }

    /**
     * @return string
     */
    public function getEmbedTemplate()
    {
        return 'MediaMonksSonataMediaBundle:Provider:soundcloud_embed.html.twig';
    }
}
