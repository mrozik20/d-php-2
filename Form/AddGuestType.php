<?php

namespace CoupleBundle\Form;

use AppBundle\Entity\Guest;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddGuestType extends AbstractType
{
    protected $couple;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $couple       = $options['couple'];
        $opt          = $options['opt'];
        $editingGuest = $options['editing_guest'];

        $builder
            ->add('imageFile', FileType::class, array(
                'required' => false,
                'attr'     => array(
                    'accept' => 'image/*'
                ),
            ))
            ->add('firstName', TextType::class, array(
                'required'           => true,
                'constraints'        => [
                    new NotBlank(),
                    new Regex(
                        [
                            'pattern' => '/^[A-Za-z]+$/'
                        ]
                    )
                ],
                'label'              => 'couple.guestslist.add_form.first_name_label',
                'translation_domain' => 'CoupleBundle',
            ))
            ->add('lastName', TextType::class, array(
                'required'           => true,
                'constraints'        => [
                    new NotBlank(),
                    new Regex(
                        [
                            'pattern' => '/^[A-Za-z]+$/'
                        ]
                    )
                ],
                'label'              => 'couple.guestslist.add_form.last_name_label',
                'translation_domain' => 'CoupleBundle',
            ))
            ->add('email', EmailType::class, array(
                'required'           => true,
                'constraints'        => [
                    new NotBlank(),
                    new Email()
                ],
                'label'              => 'couple.guestslist.add_form.email_label',
                'translation_domain' => 'CoupleBundle',
            ))
            ->add('partner', EntityType::class, array(
                'required'           => false,
                'class'              => Guest::class,
                'choice_label'       => function ($partner) {
                    return $partner->getFirstName() . ' ' . $partner->getLastName();
                },
                'multiple'           => false,
                'query_builder'      => function (EntityRepository $em) use ($couple, $opt, $editingGuest) {
                    $query = $em->createQueryBuilder('guest')
                                ->where('guest.couple = :couple')
                                ->setParameter('couple', $couple)
                                ->orderBy('guest.createdAt', 'DESC');
                    if ($opt == 'edit') {
                        $query->andWhere('(guest.partner is NULL or guest.partner = :editing_guest)')
                              ->setParameter('editing_guest', $editingGuest)
                              ->andWhere('guest.id != :editing_guest');
                    } else {
                        $query->andWhere('guest.partner is NULL');
                    }
                    $query->orderBy('guest.createdAt', 'DESC');

                    return $query;
                },
                'label'              => 'couple.guestslist.add_form.partner_label',
                'placeholder'        => 'couple.guestslist.add_form.partner_placeholder',
                'translation_domain' => 'CoupleBundle',
            ))
            ->add('listStatus', ChoiceType::class, array(
                'required'           => true,
                'choices'            => Guest::getGuestListStatus(),
                'multiple'           => false,
                'expanded'           => true,
                'label'              => 'couple.guestslist.add_form.list_type.main_label',
                'choice_label'       => function ($choiceValue, $key, $value) {
                    return 'couple.guestslist.add_form.list_type.' . $value;
                },
                'translation_domain' => 'CoupleBundle',
            ))
            ->add('roll', ChoiceType::class, array(
                'required'           => true,
                'choices'            => Guest::getGuestRollType(),
                'multiple'           => false,
                'expanded'           => true,
                'translation_domain' => 'CoupleBundle',
                'label'              => 'couple.guestslist.add_form.roll_type.main_label',
                'choice_label'       => function ($choiceValue, $key, $value) {
                    return 'couple.guestslist.add_form.roll_type.' . $value;
                },
            ))
            ->add('invitedBy', ChoiceType::class, array(
                'required'           => true,
                'choices'            => Guest::getInvitedByTypes(),
                'multiple'           => false,
                'expanded'           => true,
                'translation_domain' => 'CoupleBundle',
                'label'              => 'couple.guestslist.add_form.invited_by.main_label',
                'choice_label'       => function ($choiceValue, $key, $value) use ($couple) {
                    if ($value == 2) {
                        return $couple->getFirstUserName();
                    } elseif ($value == 3) {
                        return $couple->getSecondUserName();
                    } else {
                        return 'couple.guestslist.add_form.invited_by.' . $key;
                    }
                },
            ))
            ->add('rsvp', ChoiceType::class, array(
                'required'           => true,
                'choices'            => Guest::getGuestRsvpStatus(),
                'multiple'           => false,
                'expanded'           => true,
                'translation_domain' => 'CoupleBundle',
                'label'              => 'couple.guestslist.add_form.rsvp_status.main_label',
                'choice_label'       => function ($choiceValue, $key, $value) {
                    return 'couple.guestslist.add_form.rsvp_status.' . $value;
                },
            ))
            ->add('relation', ChoiceType::class, array(
                'required'           => true,
                'choices'            => Guest::getGuestRelationType(),
                'multiple'           => false,
                'expanded'           => true,
                'translation_domain' => 'CoupleBundle',
                'label'              => 'couple.guestslist.add_form.relation.main_label',
                'choice_label'       => function ($choiceValue, $key, $value) {
                    return 'couple.guestslist.add_form.relation.' . $value;
                },
            ))
            ->add('civilStatus', ChoiceType::class, array(
                'required'           => true,
                'choices'            => Guest::getGuestCivilStatusType(),
                'multiple'           => false,
                'expanded'           => true,
                'translation_domain' => 'CoupleBundle',
                'label'              => 'couple.guestslist.add_form.civil_status.main_label',
                'choice_label'       => function ($choiceValue, $key, $value) {
                    return 'couple.guestslist.add_form.civil_status.' . $value;
                },
            ))
            ->add('description', TextareaType::class, array(
                'required'           => false,
                'translation_domain' => 'CoupleBundle',
                'label' => 'couple.guestslist.add_form.description_label',
            ))
            ->add('address', TextType::class, array(
                'required'           => false,
                'translation_domain' => 'CoupleBundle',
                'label' => 'couple.guestslist.add_form.address_label'
            ))
            ->add('phone', TextType::class, array(
                'required'           => false,
                'translation_domain' => 'CoupleBundle',
                'label' => 'couple.guestslist.add_form.phone_label'
            ))
            ->add('allergy', TextType::class, array(
                'required'           => false,
                'translation_domain' => 'CoupleBundle',
                'label' => 'couple.guestslist.add_form.allergy_label'
            ))
            ->add('disability', TextType::class, array(
                'required'           => false,
                'translation_domain' => 'CoupleBundle',
                'label' => 'couple.guestslist.add_form.disability_label'
            ))
            ->add('arriving', TextType::class, array(
                'required'           => false,
                'translation_domain' => 'CoupleBundle',
                'label' => 'couple.guestslist.add_form.arriving_label'
            ))
            ->add('departure', TextType::class, array(
                'required'           => false,
                'translation_domain' => 'CoupleBundle',
                'label' => 'couple.guestslist.add_form.departure_label'
            ))
            ->add('gender', ChoiceType::class, array(
                'required'           => false,
                'choices'            => Guest::getGuestGenderType(),
                'multiple'           => false,
                'expanded'           => true,
                'placeholder'        => false,
                'translation_domain' => 'CoupleBundle',
                'label'              => 'couple.guestslist.add_form.gender.main_label',
                'choice_label'       => function ($choiceValue, $key, $value) {
                    return 'couple.guestslist.add_form.gender.' . $value;
                },
            ))
            ->add('ageGroup', ChoiceType::class, array(
                'required'           => false,
                'choices'            => Guest::getGuestAgeGroupType(),
                'multiple'           => false,
                'expanded'           => true,
                'placeholder'        => false,
                'translation_domain' => 'CoupleBundle',
                'label'              => 'couple.guestslist.add_form.age_group.main_label',
                'choice_label'       => function ($choiceValue, $key, $value) {
                    return 'couple.guestslist.add_form.age_group.' . $value;
                },
            ))
            ->add('age', ChoiceType::class, array(
                'required'           => false,
                'empty_data'         => null,
                'choices'            => range(0, 200),
                'multiple'           => false,
                'translation_domain' => 'CoupleBundle',
                'choice_label'       => function ($choiceValue, $key, $value) {
                    return $value;
                },
                'label'              => 'couple.guestslist.add_form.age_label',
            ))
/*            ->add('save', SubmitType::class, array(
                'translation_domain' => 'CoupleBundle',
                'label' => 'couple.guestslist.add_form.save'
            ))*/;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    => Guest::class,
            'couple'        => null,
            'opt'           => null,
            'editing_guest' => null
        ));
    }
}