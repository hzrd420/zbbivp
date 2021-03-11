<?php
declare(strict_types=1);
namespace Model;

class Interested extends Base {
  use \Validation\Traits\CortexTrait;
  public const FIRST_NAME_MIN_LENGTH = 1;
  public const FIRST_NAME_MAX_LENGTH = 200;
  public const SURNAME_MIN_LENGTH = 1;
  public const SURNAME_MAX_LENGTH = 200;
  public const BIRTH_LOCATION_MIN_LENGTH = 1;
  public const BIRTH_LOCATION_MAX_LENGTH = 200;
  public const MARITAL_STATUS = [
    'unknown',
    'single',
    'married',
    'divorced',
    'widowed'
  ];
  public const ADDRESS_MIN_LENGTH = 5;
  public const ADDRESS_MAX_LENGTH = 500;
  public const LEGAL_REPRESENTATIVE_MIN_LENGTH = 5;
  public const LEGAL_REPRESENTATIVE_MAX_LENGTH = 400;
  public const NATIONALITY_MIN_LENGTH = 4;
  public const NATIONALITY_MAX_LENGTH = 100;
  public const PHONE_MIN_LENGTH = 5;
  public const PHONE_MAX_LENGTH = 100;
  public const EMAIL_MIN_LENGTH = 1;
  public const EMAIL_MAX_LENGTH = 200;
  public const EMAIL_LEGAL_REPRESENTATIVE_MIN_LENGTH = 1;
  public const EMAIL_LEGAL_REPRESENTATIVE_MAX_LENGTH = 200;
  public const LAST_GRADUATION_MIN_LENGTH = 1;
  public const LAST_GRADUATION_MAX_LENGTH = 200;
  public const LAST_SCHOOL_MIN_LENGTH = 1;
  public const LAST_SCHOOL_MAX_LENGTH = 200;
  public const GERMAN_LEVEL = [
    'A1', 'A2', 'A3', 'B1', 'B2', 'B3', 'C1', 'C2', 'C3'
  ];
  public const DEGREE_OF_VISUAL_IMPAIRMENT = [
    1, 2, 5
  ];
  public const OTHER_DISABILITY = [
    'K', 'P'
  ];
  public const PENSION_INSURANCE_NUMBER_LENGTH = 12;
  public const TAX_ID_LENGTH = 11;
  public const TAX_CLASS = [
    'I', 'II', 'III', 'IV', 'V', 'VI'
  ];
  public const DENOMINATION = [
    'evangelical', 'catholic', 'muslim', 'budgetary', 'jewish'
  ];
  public const HEALTH_INSURANCE_NAME_MIN_LENGTH = 1;
  public const HEALTH_INSURANCE_NAME_MAX_LENGTH = 200;
  public const HEALTH_INSURANCE_NUMBER_MIN_LENGTH = 1;
  public const HEALTH_INSURANCE_NUMBER_MAX_LENGTH = 30;
  public const PAYMENT_OF_SV_CONTRIBUTIONS = [
    'payer', 'administrationStaff'
  ];

  protected $fieldConf = [
    'firstName' => [
      'type' => 'varchar(' . self::FIRST_NAME_MAX_LENGTH . ')',
      'passThrough' => true,
      'nullable' => false,
      'accepted' => true,
      'necessaryPost' => true,
      'filter' => 'trim',
      'validate' => 'required|min_len,' . self::FIRST_NAME_MIN_LENGTH . '|max_len,' . self::FIRST_NAME_MAX_LENGTH
    ],
    'surname' => [
      'type' => 'varchar(' . self::SURNAME_MAX_LENGTH . ')',
      'passThrough' => true,
      'nullable' => false,
      'accepted' => true,
      'necessaryPost' => true,
      'filter' => 'trim',
      'validate' => 'required|min_len,' . self::SURNAME_MIN_LENGTH . '|max_len,' . self::SURNAME_MAX_LENGTH
    ],
    'address' => [
      'type' => 'varchar(' . self::ADDRESS_MAX_LENGTH . ')',
      'passThrough' => true,
      'nullable' => false,
      'accepted' => true,
      'necessaryPost' => true,
      'filter' => 'trim',
      'validate' => 'required|min_len,' . self::ADDRESS_MIN_LENGTH . '|max_len,' . self::ADDRESS_MAX_LENGTH
    ],
    'nationality' => [
      'type' => 'varchar(' . self::NATIONALITY_MAX_LENGTH . ')',
      'passThrough' => true,
      'nullable' => false,
      'accepted' => true,
      'necessaryPost' => true,
      'filter' => 'trim',
      'validate' => 'required|min_len,' . self::NATIONALITY_MIN_LENGTH . '|max_len,' . self::NATIONALITY_MAX_LENGTH
    ],
    'birthDate' => [
      'type' => \DB\SQL\Schema::DT_DATE,
      'nullable' => false,
      'accepted' => true,
      'necessaryPost' => true,
      'filter' => 'trim',
      'validate' => 'required|date',
    ],
    'email' => [
      'type' => 'varchar(' . self::EMAIL_MAX_LENGTH . ')',
      'passThrough' => true,
      'nullable' => false,
      'accepted' => true,
      'necessaryPost' => true,
      'filter' => 'trim',
      'validate' => 'required|valid_email|max_len,' . self::EMAIL_MAX_LENGTH
    ],
    'birthLocation' => [
      'type' => 'varchar(' . self::BIRTH_LOCATION_MAX_LENGTH . ')',
      'passThrough' => true,
      'accepted' => true,
      'filter' => 'trim',
      'validate' => 'min_len,' . self::BIRTH_LOCATION_MIN_LENGTH . '|max_len,' . self::BIRTH_LOCATION_MAX_LENGTH
    ],
    'maritalStatus' => [
      'type' => 'varchar(100)',
      'passThrough' => true,
      'accepted' => true,
      'necessaryPost' => true,
      'filter' => 'trim',
      'item' => self::MARITAL_STATUS
    ],
    'hasChilds' => [
      'type' => \DB\SQL\Schema::DT_BOOLEAN,
      'default' => false,
      'nullable' => false,
      'accepted' => true
    ],
    'phoneMobile' => [
      'type' => 'varchar(' . self::PHONE_MAX_LENGTH . ')',
      'passThrough' => true,
      'nullable' => false,
      'accepted' => true,
      'necessaryPost' => true,
      'filter' => 'trim',
      'validate' => 'required|phone_number|max_len,' . self::PHONE_MAX_LENGTH
    ],
    'phonePrivate' => [
      'type' => 'varchar(' . self::PHONE_MAX_LENGTH . ')',
      'passThrough' => true,
      'accepted' => true,
      'filter' => 'trim',
      'validate' => 'phone_number|max_len,' . self::PHONE_MAX_LENGTH
    ],
    'legalRepresentative' => [
      'type' => 'varchar(' . self::LEGAL_REPRESENTATIVE_MAX_LENGTH . ')',
      'passThrough' => true,
      'accepted' => true,
      'filter' => 'trim',
      'validate' => 'min_len,' . self::BIRTH_LOCATION_MIN_LENGTH . '|max_len,' . self::BIRTH_LOCATION_MAX_LENGTH
    ],
    'emailLegalRepresentative' => [
      'type' => 'varchar(' . self::EMAIL_LEGAL_REPRESENTATIVE_MAX_LENGTH . ')',
      'passThrough' => true,
      'accepted' => true,
      'filter' => 'trim',
      'validate' => 'valid_email|max_len,' . self::EMAIL_MAX_LENGTH
    ],
    'phoneLegalRepresentative' => [
      'type' => 'varchar(' . self::PHONE_MAX_LENGTH . ')',
      'passThrough' => true,
      'accepted' => true,
      'filter' => 'trim',
      'validate' => 'phone_number|max_len,' . self::PHONE_MAX_LENGTH
    ],
    'lastGraduation' => [
      'type' => 'varchar(' . self::LAST_GRADUATION_MAX_LENGTH . ')',
      'passThrough' => true,
      'nullable' => false,
      'accepted' => true,
      'necessaryPost' => true,
      'filter' => 'trim',
      'validate' => 'required|min_len,' . self::LAST_GRADUATION_MIN_LENGTH . '|max_len,' . self::LAST_GRADUATION_MAX_LENGTH
    ],
    'graduationYear' => [
      'type' => \DB\SQL\Schema::DT_INT,
      'accepted' => true,
      'filter' => 'optionalInt,true',
      'validate' => 'integer'
    ],
    'lastSchool' => [
      'type' => 'varchar(' . self::LAST_SCHOOL_MAX_LENGTH . ')',
      'passThrough' => true,
      'nullable' => false,
      'accepted' => true,
      'necessaryPost' => true,
      'filter' => 'trim',
      'validate' => 'required|min_len,' . self::LAST_SCHOOL_MIN_LENGTH . '|max_len,' . self::LAST_SCHOOL_MAX_LENGTH
    ],
    'schoolFrom' => [
      'type' => \DB\SQL\Schema::DT_DATE,
      'accepted' => true,
      'filter' => 'trim',
      'validate' => 'date',
    ],
    'schoolTo' => [
      'type' => \DB\SQL\Schema::DT_DATE,
      'accepted' => true,
      'filter' => 'trim',
      'validate' => 'date',
    ],
    'hasBoardingSchoolExperience' => [
      'type' => \DB\SQL\Schema::DT_BOOLEAN,
      'default' => false,
      'nullable' => false,
      'accepted' => true
    ],
    'germanLevel' => [
      'type' => 'char(2)',
      'passThrough' => true,
      'nullable' => false,
      'accepted' => true,
      'necessaryPost' => true,
      'filter' => 'trim',
      'item' => self::GERMAN_LEVEL
    ],
    'degreeOfVisualImpairment' => [
      'type' => \DB\SQL\Schema::DT_INT,
      'nullable' => false,
      'accepted' => true,
      'necessaryPost' => true,
      'filter' => 'trim',
      'item' => self::DEGREE_OF_VISUAL_IMPAIRMENT
    ],
    'otherDisability' => [
      'type' => 'char(1)',
      'passThrough' => true,
      'accepted' => true,
      'filter' => 'trim',
      'item' => self::OTHER_DISABILITY
    ],
    'requiredAccessibilityTools' => [
      'type' => \DB\SQL\Schema::DT_LONGTEXT,
      'accepted' => true,
      'filter' => 'trim'
    ],
    'handicappedIdAvailable' => [
      'type' => \DB\SQL\Schema::DT_BOOLEAN,
      'default' => false,
      'nullable' => false,
      'accepted' => true
    ],
    'medicalRemarks' => [
      'type' => \DB\SQL\Schema::DT_LONGTEXT,
      'accepted' => true,
      'filter' => 'trim'
    ],
    'pensionInsuranceNumber' => [
      'type' => 'char(' . self::PENSION_INSURANCE_NUMBER_LENGTH . ')',
      'passThrough' => true,
      'accepted' => true,
      'filter' => 'trim',
      'validate' => 'exact_len,' . self::PENSION_INSURANCE_NUMBER_LENGTH
    ],
    'taxID' => [
      'type' => 'char(' . self::TAX_ID_LENGTH . ')',
      'passThrough' => true,
      'accepted' => true,
      'filter' => 'trim',
      'validate' => 'exact_len,' . self::TAX_ID_LENGTH
    ],
    'taxClass' => [
      'type' => 'char(2)',
      'passThrough' => true,
      'accepted' => true,
      'filter' => 'trim',
      'item' => self::TAX_CLASS
    ],
    'denomination' => [
      'type' => 'varchar(50)',
      'passThrough' => true,
      'accepted' => true,
      'filter' => 'trim',
      'item' => self::DENOMINATION
    ],
    'healthInsuranceName' => [
      'type' => 'varchar(' . self::HEALTH_INSURANCE_NAME_MAX_LENGTH . ')',
      'passThrough' => true,
      'accepted' => true,
      'filter' => 'trim',
      'validate' => 'min_len,' . self::HEALTH_INSURANCE_NAME_MIN_LENGTH . '|max_len,' . self::HEALTH_INSURANCE_NAME_MAX_LENGTH
    ],
    'healthInsuranceNumber' => [
      'type' => 'varchar(' . self::HEALTH_INSURANCE_NUMBER_MAX_LENGTH . ')',
      'passThrough' => true,
      'accepted' => true,
      'filter' => 'trim',
      'validate' => 'min_len,' . self::HEALTH_INSURANCE_NUMBER_MIN_LENGTH . '|max_len,' . self::HEALTH_INSURANCE_NUMBER_MAX_LENGTH
    ],
    'membershipCertificateForHealthInsuranceAvailable' => [
      'type' => \DB\SQL\Schema::DT_BOOLEAN,
      'default' => false,
      'nullable' => false,
      'accepted' => true
    ],
    'paymentOfSVContributions' => [
      'type' => 'varchar(50)',
      'passThrough' => true,
      'accepted' => true,
      'filter' => 'trim',
      'item' => self::PAYMENT_OF_SV_CONTRIBUTIONS
    ],
    'retraining' => [
      'type' => \DB\SQL\Schema::DT_BOOLEAN,
      'default' => false,
      'nullable' => false,
      'accepted' => true
    ],
    'electives' => [
      'type' => \DB\SQL\Schema::DT_LONGTEXT,
      'accepted' => true,
      'filter' => 'trim'
    ],
    'trainingCourse1Id' => [
      'belongs-to-one' => '\Model\TrainingCourse',
      'nullable' => false,
      'accepted' => true,
      'necessaryPost' => true
    ],
    'trainingCourse2Id' => [
      'belongs-to-one' => '\Model\TrainingCourse',
      'accepted' => true,
      'necessaryPost' => true
    ],
    'steps' => [
      'has-many' => ['\Model\Step', 'interestedId']
    ]
  ];
  protected $table = 'interested';
  public $sortableFields = [
    'firstName' => 'firstName',
    'surname' => 'surname',
    'birthDate' => 'birthDate',
    'birthLocation' => 'birthLocation'
  ];

  /**
   * Check foreign Key of trainingCourse1Id
   * @param string $value The value
   */
  protected function set_trainingCourse1Id($value) {
    return $this->checkForeignKey($this->rel('trainingCourse1Id'), $value, 'trainingCourse1Id');
  } // set_trainingCourse2Id()

  /**
   * Check optional foreign Key of trainingCourse2
   * @param string $value The value
   */
  protected function set_trainingCourse2Id($value) {
    if ($value === 'none')
      return null;
    return $this->checkForeignKey($this->rel('trainingCourse2Id'), $value, 'trainingCourse2Id');
  } // set_trainingCourse2Id()

  /**
   * Get newest step of loaded interest
   */
  public function getNewestStep() {
    if ($this->dry() || is_null($this->steps))
      return null;
    return $this->steps[count($this->steps)-1];
  } // getNewestStep()
} // class