<?php

namespace App\Form;

use App\Entity\Cita;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CitaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $estados = $options['estados'];

        $builder
            ->add('fecha', DateTimeType::class, array(
                'label' => 'Fecha',
                'widget' => 'single_text',
                'attr' => array(
                    'class' => 'js-datepicker form-control'
                ),
            ))
            ->add('estado', ChoiceType::class, array(
                'choices' => $estados,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('paciente')
            ->add('especialidad')
            ->add('paquete')
            ->add('servicio')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cita::class,
            'estados' => 'estados'
        ]);
    }
}
