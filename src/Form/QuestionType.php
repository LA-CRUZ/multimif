<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('texte')
            ->add('reponses', CollectionType::class, [
                'entry_type' => ReponseType::class,
                'entry_options' => ['label' => false],
            ])
            ->add('add', SubmitType::class, [
                'attr' => ['class' => 'btn'],
                'label' => 'Ajouter une autre question'
            ])
            ->add('end', SubmitType::class, [
                'attr' => ['class' => 'btn'],
                'label' => 'Terminer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
