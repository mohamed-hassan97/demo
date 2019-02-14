<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = \Faker\Factory::create('fr_FR');

        for ($i=1; $i < mt_rand(4,5); $i++) { 
            $category = new Category();
            $category->setTitle($faker->sentence())
                ->setDescription($faker->paragraph());

            $manager->persist($category);
        }



    	for ($j=1; $j <= mt_rand(4,6); $j++) { 
    		$article  = new Article();
            $content = '<p>'.join($faker->paragraphs(5),'</p><p>').'</p>';

    		$article->setTitle($faker->sentence)
    		->setContent($content)
    		->setImage($faker->imageUrl())
    		->setCreateAt($faker->dateTimeBetween('-6 months'))
            ->setCategory($category);

    		$manager->persist($article);

            //on donne des commentaires a l'article
            for ($k=1; $k <= mt_rand(4,10); $k++) { 
                $comment = new Comment();

                $content = '<p>'.join($faker->paragraphs(2),'</p><p>') . '</p>';
                $now =  new \DateTime();
                $interval = $now->diff($article->getCreateAt());
                $days = $interval->days;
                $minimum = '-'.$days.' days'; // ex: -100 days

                $comment->setAuthor($faker->name)
                        ->setContent($content)
                        ->setCreateAt($faker->dateTimeBetween($minimum))
                        ->setArticle($article);
                $manager->persist($comment);

            }

    	}

        $manager->flush();
    }
}
