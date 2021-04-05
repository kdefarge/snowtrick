<?php

/**
 * Category management
 *
 * A service to manage the creation, uniqueness and deletion of categories
 *
 * @author     Kevin DEFARGE <kdefarge@gmail.com>
 */

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

    public function __construct(
        EntityManagerInterface $entityManager,
        TrickRepository $trickRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->entityManager = $entityManager;
        $this->trickRepository = $trickRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Find or create a category from a name
     * 
     * Look for the category in the database, if the category exists, 
     * it returned otherwise a new category entity is created in 
     * the database before being returned
     *
     * @return Category
     */
    public function findOrCreateCategory(string $categoryName): Category
    {
        $category = $this->categoryRepository->findOneBy(['name' => $categoryName]);
        if ($category instanceof Category)
            return $category;

        $category = new Category();
        $category->setName($categoryName);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }

    /**
     * Destroy an unused category
     * 
     * Destroys a category after checking that it is not used in a figure
     *
     * @return bool
     */
    public function deleteNotUsedCategory(Category $category): bool
    {
        $trick = $this->trickRepository->findOneBy(['category' => $category]);
        if ($trick instanceof Trick)
            return false;

        $this->entityManager->remove($category);
        $this->entityManager->flush();
        return true;
    }
}
