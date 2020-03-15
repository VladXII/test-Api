<?php

namespace App\Form\Product;

use App\Entity\Brand\Brand;
use App\Entity\Product\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('npm', TextType::class, [
                'required' => true
            ])
            ->add('brand', EntityType::class, [
                'class' => Brand::class
            ])
            ->add('name', TextType::class, [
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
