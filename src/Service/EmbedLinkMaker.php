<?php

/**
 * A service to check video links
 *
 * Checking the links of Youtube, Dailymotion and Vimeo videos to get valid embedded links
 *
 * @author     Kevin DEFARGE <kdefarge@gmail.com>
 */

namespace App\Service;

class EmbedLinkMaker
{
    const HOST_YOUTU        = 'youtu.be';
    const HOST_YOUTUBE      = 'www.youtube.com';
    const HOST_VIMEO        = 'vimeo.com';
    const HOST_DAILYMOTION  = 'www.dailymotion.com';

    const EMBED_YOUTUBE     = 'https://www.youtube-nocookie.com/embed/';
    const EMBED_VIMEO       = 'https://player.vimeo.com/video/';
    const EMBED_DAILYMOTION = 'https://www.dailymotion.com/embed/video/';

    /**
     * Find or create a category from a name
     * 
     * Checking the links of Youtube, Dailymotion and Vimeo videos to get 
     * valid embedded links. If the check fails, the function returns null
     *
     * @return null|string
     */
    public function create(string $url): ?string
    {
        $parse = parse_url($url);

        if (!isset($parse) || !isset($parse['host']) || !isset($parse['path']))
            return null;

        switch ($parse['host']) {
            case self::HOST_YOUTU:
                return $this->isYoutuBe($parse);
            case self::HOST_YOUTUBE:
                return $this->isYoutubeCom($parse);
            case self::HOST_VIMEO:
                return $this->isViemeoCom($parse);
            case self::HOST_DAILYMOTION:
                return $this->isDailymotionCom($parse);
        }

        return null;
    }

    private function isYoutuBe($parse): ?string
    {
        return self::EMBED_YOUTUBE . substr($parse['path'], 1);
    }

    private function isYoutubeCom($parse): ?string
    {
        if (!isset($parse['query']))
            return null;

        parse_str($parse['query'], $data);

        if (isset($data['v']));
        return self::EMBED_YOUTUBE . $data['v'];

        return null;
    }

    private function isViemeoCom($parse): ?string
    {
        return self::EMBED_VIMEO . substr($parse['path'], 1);
    }

    private function isDailymotionCom($parse): ?string
    {
        $rest = substr($parse['path'], 7);

        if ($rest)
            return self::EMBED_DAILYMOTION . $rest;

        return null;
    }
}
