<?php

namespace App\Form;

use App\Entity\Hobby;
use App\Entity\Job;
use App\Entity\Personne;
use App\Entity\Profile;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PersonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname')
            ->add('name')
            ->add('age')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt')
            ->add('profile', EntityType::class, [
                'required' => false,
                'class' => Profile::class,
                'attr' => [
                    'class' => 'select2',
                ],
            ])
            ->add('hobbies', EntityType::class, [
                'required' => false,
                'expanded' => false,
                'multiple' => true,
                'class' => Hobby::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('h')
                        ->orderBy('h.designation', 'ASC');
                },
                'choice_label' => function(?Hobby $hobby) {
                    return $hobby->getDesignation() . ' (' . $hobby->getId() . ')';
                },
                'label' => 'Hobbies LABEL',
                'attr' => [
                    'class' => 'select2',
                ],
            ])
            ->add('job', EntityType::class, [
                'required' => false,
                'class' => Job::class,
                'attr' => [
                    'class' => 'select2',
                ],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Votre image de profil (Des fichiers images uniquement)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Télécharger une image valide',
                    ])
                ],
            ])
            ->add('edit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);
    }
}
