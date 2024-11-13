<?php

namespace App\Form\Type;

use App\Entity\Visibility;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class VideoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return $this|void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Titre (Obligatoire)',
                ]
            )
            ->add(
                'description',
                TextAreaType::class,
                [
                    'label' => 'Description',
                    'required' => false,
                ]
            )
            ->add(
                'thumbnail',
                FileType::class,
                [
                    'label' => 'Miniature',
                ]
            )
            ->add(
                'videoFile',
                FIleType::class,
                [
                    'label' => 'Vidéo'
                ]
            )
            ->add(
                'visibility',
                EntityType::class,
                [
                    'class' => Visibility::class,
                    'choice_label' => 'label',
                    'expanded' => true,
                    'multiple' => false,
                    'label' => 'Visibilité',
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                   'attr' => [
                       'class' => 'btn btn-md btn-primary',
                   ]
                ]
            )
        ;

        return $this;
    }
}