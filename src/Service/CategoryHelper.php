<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Trick;
use App\Repository\CategoryRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;

class CategoryHelper
{
    private $entityManager;
    private $trickRepository;
    private $categoryRepository;
    
    public function __construct( EntityManagerInterface $entityManager,
        TrickRepository $trickRepository, CategoryRepository $categoryRepository)
    {
        $this->entityManager = $entityManager;
        $this->trickRepository = $trickRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function findOrCreateCategory(string $categoryName) : Category
    {
        $category = $this->categoryRepository->findOneBy(['name' => $categoryName]);
        if($category instanceof Category)
            return $category;
        
        $category = new Category();
        $category->setName($categoryName);

        $this->entityManager->persist($category);
        $this->entityManager->flush();
        
        return $category;
    }

    public function deleteNotUsedCategory(Category $category) : bool
    {
        $trick = $this->trickRepository->findOneBy(['category'=>$category]);
        if ($trick instanceof Trick)
            return false;
        
        $this->entityManager->remove($category);
        $this->entityManager->flush();
        return true;
    }
}
