<?php

namespace CoupleBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Guest;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class GuestType extends AddGuestType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);

        $builder->remove('partner');
        $builder->remove('listStatus');
        $builder->remove('roll');
        $builder->remove('invitedBy');
        $builder->remove('rsvp');
        $builder->remove('relation');
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class'    => Guest::class,
            'couple'        => null,
            'opt'           => null,
            'editing_guest' => null
        ));
    }

    public function getName() {
        return 'add_guest';
    }

    public function getBlockPrefix() {
        return "add_guest";
    }
}
