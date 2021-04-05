<?php

namespace App\DataFixtures\Providers;

use App\Entity\Category;
use App\Entity\User;
use App\Service\CategoryHelper;
use App\Service\EmbedLinkMaker;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FixturesHelperProviders
{
    const DIR_PROFILPICTURE = "profilpicture";
    const DIR_TRICK = "trick";

    private $encoder;
    private $params;
    private $categoryHelper;
    private $embedLinkMaker;

    public function __construct(
        UserPasswordEncoderInterface $encoder,
        ParameterBagInterface $params,
        CategoryHelper $categoryHelper,
        EmbedLinkMaker $embedLinkMaker
    ) {
        $this->encoder = $encoder;
        $this->params = $params;
        $this->categoryHelper = $categoryHelper;
        $this->embedLinkMaker = $embedLinkMaker;
        $dirname = $params->get('upload_directory') . $this::DIR_PROFILPICTURE;
        array_map('unlink', glob("$dirname/*.*"));
        $dirname = $params->get('upload_directory') . $this::DIR_TRICK;
        array_map('unlink', glob("$dirname/*.*"));
        $this->makeUploadedDir($this::DIR_PROFILPICTURE);
        $this->makeUploadedDir($this::DIR_TRICK);
    }

    private function makeUploadedDir(string $dirName)
    {
        $dirName = $this->params->get('upload_directory') . $dirName;
        if (!is_dir($dirName)) {
            if (false === @mkdir($dirName, 0777, true) && !is_dir($dirName)) {
                throw new FileException(sprintf('Unable to create the "%s" directory.', $dirName));
            }
        } elseif (!is_writable($dirName)) {
            throw new FileException(sprintf('Unable to write in the "%s" directory.', $dirName));
        }
    }

    public function helpEncodePassword(string $plainPassword): string
    {
        return $this->encoder->encodePassword(new User(), $plainPassword);
    }

    public function userPicture(array $fileNames): string
    {
        return $this->uploadedMaker($fileNames, "profilpicture");
    }

    public function trickPicture(array $fileNames): string
    {
        return $this->uploadedMaker($fileNames, "trick");
    }

    private function uploadedMaker(array $fileNames, $nameDir): string
    {
        $filename = $fileNames[array_rand($fileNames)];
        $projetDest = $nameDir . '/' . uniqid() . '_' . $filename;

        $source = $this->params->get('fixtures_directory')
            . $nameDir . '/' . $filename;

        $dest = $this->params->get('upload_directory') . $projetDest;

        copy($source, $dest);

        return $projetDest;
    }
    
    public function embedLinkMaker($link): string
    {
        return $this->embedLinkMaker->create($link);
    }

    public function findOrCreateCategory(string $categoryName): Category
    {
        return $this->categoryHelper->findOrCreateCategory($categoryName);
    }
}
