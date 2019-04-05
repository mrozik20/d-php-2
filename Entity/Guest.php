<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use PHPThumb\GD;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AppBundle\Interfaces\ImagePathInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * Guest
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="guest")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GuestRepository")
 **/
class Guest implements ImagePathInterface
{
    const IMAGE_WIDTH = 95;
    const IMAGE_HEIGHT = 95;

    const STATUS_LIST_PRIMARY = "primary";
    const STATUS_LIST_SECONDARY = "secondary";

    public static function getGuestListStatus()
    {
        return array(
            self::STATUS_LIST_PRIMARY,
            self::STATUS_LIST_SECONDARY
        );
    }

    const STATUS_RSVP_NO_ANSWER = 'not_answered';
    const STATUS_RSVP_COMING = 'coming';
    const STATUS_RSVP_NOT_COMING = 'not_coming';
    const STATUS_RSVP_MAYBE_COMING = 'maybe';

    public static function getGuestRsvpStatus()
    {
        return array(
            self::STATUS_RSVP_NO_ANSWER,
            self::STATUS_RSVP_COMING,
            self::STATUS_RSVP_NOT_COMING,
            self::STATUS_RSVP_MAYBE_COMING
        );
    }

    const TYPE_ROLL_GUEST = 'guest';
    const TYPE_ROLL_TOASTMASTER = 'toastmaster';
    const TYPE_ROLL_BESTMAN = 'bestman';
    const TYPE_ROLL_HONORARY_BRIDESMAID = 'honorary_bridesmaid';
    const TYPE_ROLL_BRIDESMAID = 'bridesmaid';
    const TYPE_ROLL_FLOWER = 'flower';

    public static function getGuestRollType()
    {
        return array(
            self::TYPE_ROLL_GUEST,
            self::TYPE_ROLL_TOASTMASTER,
            self::TYPE_ROLL_BESTMAN,
            self::TYPE_ROLL_HONORARY_BRIDESMAID,
            self::TYPE_ROLL_BRIDESMAID,
            self::TYPE_ROLL_FLOWER
        );
    }

    const TYPE_RELATION_FAMILY = 'family';
    const TYPE_RELATION_RELATIVE = 'relative';
    const TYPE_RELATION_FRIEND = 'friend';

    public static function getGuestRelationType()
    {
        return array(
            self::TYPE_RELATION_FAMILY,
            self::TYPE_RELATION_RELATIVE,
            self::TYPE_RELATION_FRIEND
        );
    }

    const TYPE_CIVIL_STATUS_SINGLE = 'single';
    const TYPE_CIVIL_STATUS_RELATIONSHIP = 'relationship';
    const TYPE_CIVIL_STATUS_FAMILY = 'family';

    public static function getGuestCivilStatusType()
    {
        return array(
            self::TYPE_CIVIL_STATUS_SINGLE,
            self::TYPE_CIVIL_STATUS_RELATIONSHIP,
            self::TYPE_CIVIL_STATUS_FAMILY
        );
    }

    const TYPE_GENDER_FEMALE = 'female';
    const TYPE_GENDER_MALE = 'male';
    const TYPE_GENDER_OTHER = 'other';

    public static function getGuestGenderType()
    {
        return array(
            self::TYPE_GENDER_FEMALE,
            self::TYPE_GENDER_MALE,
            self::TYPE_GENDER_OTHER
        );
    }

    const TYPE_AGE_ADULT = 'adult';
    const TYPE_AGE_RETIRED = 'retired';
    const TYPE_AGE_CHILD = 'child';
    const TYPE_AGE_INFANT = 'infant';

    public static function getGuestAgeGroupType()
    {
        return array(
            self::TYPE_AGE_ADULT,
            self::TYPE_AGE_RETIRED,
            self::TYPE_AGE_CHILD,
            self::TYPE_AGE_INFANT
        );
    }

    public function __construct(Couple $couple = null)
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        if ($couple) {
            $this->couple = $couple;
        }

        $this->invitations = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="hash", type="string", unique=true, nullable=false)
     */
    protected $hash;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @var string
     * @ORM\Column(name="first_name", type="string", length=255, nullable=false)
     */
    protected $firstName;

    /**
     * @var string
     * @ORM\Column(name="last_name", type="string", length=255, nullable=false)
     */
    protected $lastName;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    protected $email;

    /**
     * @Assert\File(maxSize="2048k")
     * @Assert\Image(mimeTypes={"image/jpeg", "image/jpg", "image/png","image/gif"}, mimeTypesMessage="Please upload a valid image.")
     */
    protected $imageFile;

    private $tempImagePath;

    /**
     * @var string
     * @ORM\Column(name="image", type="string", length=512, nullable=true)
     */
    protected $image;

    /**
     *
     * @var Guest
     * @ORM\OneToOne(targetEntity="Guest")
     * @ORM\JoinColumn(name="partner_id", referencedColumnName="id", nullable=true)
     */
    protected $partner = null;

    /**
     * @var string
     * @ORM\Column(name="list_status", type="string", length=255, nullable=false)
     */
    protected $listStatus = self::STATUS_LIST_PRIMARY;

    /**
     * @var string
     * @ORM\Column(name="rsvp_status", type="string", length=255, nullable=false)
     */
    protected $rsvp = self::STATUS_RSVP_NO_ANSWER;

    /**
     * @var string
     * @ORM\Column(name="roll_type", type="string", length=255, nullable=false)
     */
    protected $roll = self::TYPE_ROLL_GUEST;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Couple", inversedBy="guests")
     * @ORM\JoinColumn(name="couple_id", referencedColumnName="id", nullable=false)
     */
    protected $couple;

    /**
     * frequency possible options
     */
    public static $invitedByType = [
        'both'    => 1,
        'owner'   => 2,
        'partner' => 3,
    ];

    /**
     * @var integer
     *
     * @ORM\Column(name="invitedBy", type="integer", nullable=false)
     */
    protected $invitedBy = 1;

    /**
     * @var string
     * @ORM\Column(name="relation", type="string", length=255, nullable=false)
     */
    protected $relation = self::TYPE_RELATION_FAMILY;

    /**
     * @var string
     * @ORM\Column(name="civil_status", type="string", length=255, nullable=false)
     */
    protected $civilStatus = self::TYPE_CIVIL_STATUS_SINGLE;

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    protected $address;

    /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    protected $phone;

    /**
     * @var string
     * @ORM\Column(name="allergy", type="string", length=255, nullable=true)
     */
    protected $allergy;

    /**
     * @var string
     * @ORM\Column(name="disability", type="string", length=255, nullable=true)
     */
    protected $disability;

    /**
     * @var string
     * @ORM\Column(name="arriving", type="string", length=255, nullable=true)
     */
    protected $arriving;

    /**
     * @var string
     * @ORM\Column(name="departure", type="string", length=255, nullable=true)
     */
    protected $departure;

    /**
     * @var string
     * @ORM\Column(name="gender", type="string", length=255, nullable=true)
     */
    protected $gender = null;

    /**
     * @var string
     * @ORM\Column(name="age_group", type="string", length=255, nullable=true)
     */
    protected $ageGroup = null;

    /**
     * @var integer
     *
     * @ORM\Column(name="age", type="integer", nullable=true)
     */
    protected $age = null;

    /**
     * @ORM\ManyToMany(targetEntity="Invitations", inversedBy="guests", cascade={"persist","remove"})
     * @ORM\JoinTable(name="guest_invitations")
     */
    private $invitations;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPartner()
    {
        return $this->partner;
    }

    /**
     * @param Guest $partner
     */
    public function setPartner(Guest $partner = null)
    {
        if ($this->partner !== $partner) {
            if ($partner != null) {
                $partner->partner = $this;
            }
            if($this->partner && $this->partner->partner){
                $this->partner->partner = null;
            }
            $this->partner = $partner;
        }
    }

    /**
     * @return string
     */
    public function getListStatus()
    {
        return $this->listStatus;
    }

    /**
     * @param string $listStatus
     */
    public function setListStatus(string $listStatus)
    {
        $this->listStatus = $listStatus;
    }

    /**
     * @return string
     */
    public function getRsvp()
    {
        return $this->rsvp;
    }

    /**
     * @param string $rsvp
     */
    public function setRsvp(string $rsvp)
    {
        $this->rsvp = $rsvp;
    }

    /**
     * @return string
     */
    public function getRoll()
    {
        return $this->roll;
    }

    /**
     * @param string $roll
     */
    public function setRoll(string $roll)
    {
        $this->roll = $roll;
    }

    /**
     * @return mixed
     */
    public function getCouple()
    {
        return $this->couple;
    }

    /**
     * @param mixed $couple
     */
    public function setCouple($couple)
    {
        $this->couple = $couple;
    }

    /**
     * @return int
     */
    public function getInvitedBy()
    {
        return $this->invitedBy;
    }

    /**
     * @param int $invitedBy
     */
    public function setInvitedBy(int $invitedBy)
    {
        $this->invitedBy = $invitedBy;
    }

    /**
     * @return string
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @param string $relation
     */
    public function setRelation(string $relation)
    {
        $this->relation = $relation;
    }

    /**
     * @return string
     */
    public function getCivilStatus()
    {
        return $this->civilStatus;
    }

    /**
     * @param string $civilStatus
     */
    public function setCivilStatus(string $civilStatus)
    {
        $this->civilStatus = $civilStatus;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getAllergy()
    {
        return $this->allergy;
    }

    /**
     * @param string $allergy
     */
    public function setAllergy(string $allergy)
    {
        $this->allergy = $allergy;
    }

    /**
     * @return string
     */
    public function getDisability()
    {
        return $this->disability;
    }

    /**
     * @param string $disability
     */
    public function setDisability(string $disability)
    {
        $this->disability = $disability;
    }

    /**
     * @return string
     */
    public function getArriving()
    {
        return $this->arriving;
    }

    /**
     * @param string $arriving
     */
    public function setArriving(string $arriving)
    {
        $this->arriving = $arriving;
    }

    /**
     * @return string
     */
    public function getDeparture()
    {
        return $this->departure;
    }

    /**
     * @param string $departure
     */
    public function setDeparture(string $departure)
    {
        $this->departure = $departure;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender(string $gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getAgeGroup()
    {
        return $this->ageGroup;
    }

    /**
     * @param string $ageGroup
     */
    public function setAgeGroup(string $ageGroup)
    {
        $this->ageGroup = $ageGroup;
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge(int $age)
    {
        $this->age = $age;
    }

    static function getInvitedByTypes()
    {
        return self::$invitedByType;
    }

    static function getInvitedTypeNameByType($typeID)
    {
        foreach (self::$invitedByType as $key => $val) {
            if ($val == $typeID) {
                return $key;
            }
        }
    }

    public function getGuestImageAbsolutePath() {
        return null === $this->image ? null : $this->getUploadRootDir() . '/' . $this->image;
    }

    public function getImagePath() {
        return null === $this->image ? null : $this->getUploadDir() . '/' . $this->image;
    }

    protected function getUploadRootDir()
    {
        return __DIR__ . '/../../../web/uploads/guest/images';
    }

    protected function getUploadDir() {
        return '/uploads/guest/images';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUploadGuestImage()
    {
        if (null !== $this->getImageFile()) {
            $filename = $this->generateRandomGuestImageFilename();
            $this->setImage($filename . '.' . $this->getImageFile()->getClientOriginalExtension());
        }
    }

    public function generateRandomGuestImageFilename()
    {
        $count = 0;
        do {
            $randomString = time() . sha1(uniqid(mt_rand(), true));
            $count++;
        } while (file_exists($this->getUploadRootDir() . '/' . $randomString . '.' . $this->getImageFile()->guessExtension()) && $count < 50);

        return $randomString;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     *
     * Upload the image
     *
     * @return mixed
     */
    public function uploadImage()
    {
        if ($this->getImageFile() === null) {
            return;
        }
        $this->getImageFile()->move($this->getUploadRootDir(), $this->getImage());

        $thumbSmall = new GD($this->getGuestImageAbsolutePath());

        $thumbSmall->setOptions([
            'resizeUp' => true,
        ]);
        switch ($this->getImageFile()->getClientOriginalExtension()) {
            case "png":
                $ext = "png";
                $thumbSmall->adaptiveResize(self::IMAGE_WIDTH + 1, self::IMAGE_HEIGHT + 1);
                $thumbSmall->pad(self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
                break;
            case "gif":
                $ext = "gif";
                $thumbSmall->adaptiveResize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
                break;
            default:
                $ext = "jpg";
                $thumbSmall->adaptiveResize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
                break;
        }

        $thumbSmall->save($this->getGuestImageAbsolutePath(), $ext);

        if (isset($this->tempImagePath) && ! empty($this->tempImagePath) && file_exists($this->getUploadRootDir() . '/' . $this->tempImagePath)) {
            unlink($this->getUploadRootDir() . '/' . $this->tempImagePath);
            $this->tempImagePath = null;
        }
        $this->imageFile = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeProfileFile()
    {
        if ($this->getGuestImageAbsolutePath() && file_exists($this->getGuestImageAbsolutePath())) {
            unlink($this->getGuestImageAbsolutePath());
        }
    }

    /**
     * Sets the file used for profile  uploads
     *
     * @param UploadedFile $file
     *
     * @return object
     */
    public function setImageFile(UploadedFile $file = null)
    {
        $this->imageFile = $file;
        if (isset($this->image)) {
            $this->tempImagePath = $this->image;
            $this->image         = null;
        } else {
            $this->image = 'initial';
        }

        return $this;
    }

    /**
     * Get the file used for profile  uploads
     *
     * @return UploadedFile
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Guest
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @ORM\PrePersist()
     *
     * @return bool|string
     */
    public function preHashUpdate()
    {
        $this->setHash($this->id . substr(md5(uniqid(rand())), 0, 10));
    }

    /**
     * @return string
     */
    public function getFirstNameLetter() {
        return mb_substr($this->getFirstName(), 0, 1, 'utf-8');
    }

    public function getInvitation()
    {
        return $this->invitations;
    }

    public function addInvitation(Invitations $invitation)
    {
        if (!$this->invitations->contains($invitation)) {
            $this->invitations[] = $invitation;
        }
        return $this;
    }

    public function removeInvitation(Invitations $invitation)
    {
        $this->invitations->removeElement($invitation);
        return $this;
    }

    public function setInvitations($invitations)
    {
        $this->invitations = $invitations;
        return $this;
    }
}
