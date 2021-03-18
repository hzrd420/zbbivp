<?php
declare(strict_types=1);
namespace Controller;
/**
 * Controller for interested people
 */
class Interested extends Resource {
  protected $resourceName = 'interested';
  protected $uiActions = [
    ['addStep', 'addStep', ['interestedId' => '_id'], []],
    ['edit', 'editInterested', ['id' => '_id'], []],
    ['delete', 'deleteInterested', ['id' => '_id'], []],
  ];
  protected $reroute = '@listInterested';
  protected $hasFilter = true;

  public function __construct(\Monolog\Logger $logger, \Authentication $authentication, \Model\Interested $model) {
    parent::__construct($logger, $authentication, $model);
    // Add some counters:
    $this->model->countRel('steps');
  } // constructor

  protected function getFilters(\Model\Base $model, array $opts): array {
    $filters = [];
    // Training course 1:
    if (array_key_exists('trainingCourse1', $opts))
      $filters[] = ['trainingCourse1Id = ?', $opts['trainingCourse1']];

    // Training course 2:
    if (array_key_exists('trainingCourse2', $opts))
      $filters[] = ['trainingCourse2Id = ?', $opts['trainingCourse2']];

    // First name:
    if (array_key_exists('firstName', $opts))
      $filters[] = ['firstName LIKE ?', '%' . $opts['firstName'] . '%'];

    // Surname:
    if (array_key_exists('surname', $opts))
      $filters[] = ['surname LIKE ?', '%' . $opts['surname'] . '%'];

    // Address:
    if (array_key_exists('address', $opts))
      $filters[] = ['address LIKE ?', '%' . $opts['address'] . '%'];

    // Birth date:
    if (array_key_exists('birthDate', $opts))
      $filters[] = ['birthDate = ?', $opts['birthDate']];

    // Birth location:
    if (array_key_exists('birthLocation', $opts))
      $filters[] = ['birthLocation LIKE ?', '%' . $opts['birthLocation'] . '%'];

    // Marital status:
    if (array_key_exists('maritalStatus', $opts))
      $filters[] = ['maritalStatus = ?', $opts['maritalStatus'] === 'none' ? null : $opts['maritalStatus']];

    // Email:
    if (array_key_exists('email', $opts))
      $filters[] = ['email LIKE ?', '%' . $opts['email'] . '%'];

    // Phone private:
    if (array_key_exists('phonePrivate', $opts))
      $filters[] = ['phonePrivate LIKE ?', '%' . $opts['phonePrivate'] . '%'];

    // Phone mobile:
    if (array_key_exists('phoneMobile', $opts))
      $filters[] = ['phoneMobile LIKE ?', '%' . $opts['phoneMobile'] . '%'];

    // Legal representative:
    if (array_key_exists('legalRepresentative', $opts))
      $filters[] = ['legalRepresentative LIKE ?', '%' . $opts['legalRepresentative'] . '%'];

    // Has childs:
    if (array_key_exists('hasChilds', $opts))
      $filters[] = ['hasChilds = ?', $opts['hasChilds'] === 'true'];

    // Last Graduation:
    if (array_key_exists('lastGraduation', $opts))
      $filters[] = ['lastGraduation LIKE ?', '%' . $opts['lastGraduation'] . '%'];

    // Graduation year:
    if (array_key_exists('graduationYear', $opts))
      $filters[] = ['graduationYear = ?', $opts['graduationYear']];

    // Last school:
    if (array_key_exists('lastSchool', $opts))
      $filters[] = ['lastSchool LIKE ?', '%' . $opts['lastSchool'] . '%'];

    // School from:
    if (array_key_exists('schoolFrom', $opts))
      $filters[] = ['schoolFrom = ?', $opts['schoolFrom']];

    // School to:
    if (array_key_exists('schoolTo', $opts))
      $filters[] = ['schoolTo = ?', $opts['schoolTo']];

    // Has boarding school experience:
    if (array_key_exists('hasBoardingSchoolExperience', $opts))
      $filters[] = ['hasBoardingSchoolExperience = ?', $opts['hasBoardingSchoolExperience'] === 'true'];

    // German level:
    if (array_key_exists('germanLevel', $opts))
      $filters[] = ['germanLevel = ?', $opts['germanLevel']];

    // Degree of visual impairment:
    if (array_key_exists('degreeOfVisualImpairment', $opts))
      $filters[] = ['degreeOfVisualImpairment = ?', $opts['degreeOfVisualImpairment']];

    // Other disability:
    if (array_key_exists('otherDisability', $opts))
      $filters[] = ['otherDisability = ?', $opts['otherDisability']];
    // Handycap ID available:
    if (array_key_exists('handicappedIdAvailable', $opts))
      $filters[] = ['handicappedIdAvailable = ?', $opts['handicappedIdAvailable'] === 'true'];

    // Required accessibility tools:
    if (array_key_exists('requiredAccessibilityTools', $opts))
      $filters[] = ['requiredAccessibilityTools LIKE ?', '%' . $opts['requiredAccessibilityTools'] . '%'];

    // Medical remarks:
    if (array_key_exists('medicalRemarks', $opts))
      $filters[] = ['medicalRemarks LIKE ?', '%' . $opts['medicalRemarks'] . '%'];

    // Retraining:
    if (array_key_exists('retraining', $opts))
      $filters[] = ['retraining = ?', $opts['retraining'] === 'true'];

    // Electives:
    if (array_key_exists('electives', $opts))
      $filters[] = ['electives LIKE ?', '%' . $opts['electives'] . '%'];

    // Pension insurance number:
    if (array_key_exists('pensionInsuranceNumber', $opts))
      $filters[] = ['pensionInsuranceNumber LIKE ?', '%' . $opts['pensionInsuranceNumber'] . '%'];

    // Tax ID:
    if (array_key_exists('taxID', $opts))
      $filters[] = ['taxID LIKE ?', '%' . $opts['taxID'] . '%'];

    // Tax class:
    if (array_key_exists('taxClass', $opts))
      $filters[] = ['taxClass = ?', $opts['taxClass'] === 'none' ? null : $opts['taxClass']];

    // Denomination:
    if (array_key_exists('denomination', $opts))
      $filters[] = ['denomination = ?', $opts['denomination'] === 'none' ? null : $opts['denomination']];

    // Health insurance name:
    if (array_key_exists('healthInsuranceName', $opts))
      $filters[] = ['healthInsuranceName LIKE ?', '%' . $opts['healthInsuranceName'] . '%'];

    // Health insurance number:
    if (array_key_exists('healthInsuranceNumber', $opts))
      $filters[] = ['healthInsuranceNumber LIKE ?', '%' . $opts['healthInsuranceNumber'] . '%'];

    // Membership certificate for healt insurance available:
    if (array_key_exists('membershipCertificateForHealthInsuranceAvailable', $opts))
      $filters[] = ['membershipCertificateForHealthInsuranceAvailable = ?', $opts['membershipCertificateForHealthInsuranceAvailable'] === 'true'];

    // Payment of SV contributions:
    if (array_key_exists('paymentOfSVContributions', $opts))
      $filters[] = ['paymentOfSVContributions = ?', $opts['paymentOfSVContributions'] === 'none' ? null : $opts['paymentOfSVContributions']];

    // Orientation week interest:
    if (array_key_exists('orientationWeekInterest', $opts))
      $filters[] = ['orientationWeekInterest = ?', $opts['orientationWeekInterest'] === 'true'];

    // Orientation week from:
    if (array_key_exists('orientationWeekFrom', $opts))
      $filters[] = ['orientationWeekFrom = ?', $opts['orientationWeekFrom']];

    // Orientation week to:
    if (array_key_exists('orientationWeekTo', $opts))
      $filters[] = ['orientationWeekTo = ?', $opts['orientationWeekTo']];

    // Orientation week accommodation required:
    if (array_key_exists('orientationWeekAccommodationRequired', $opts))
      $filters[] = ['orientationWeekAccommodationRequired = ?', $opts['orientationWeekAccommodationRequired'] === 'true'];

    // Orientation week cost commitment requested:
    if (array_key_exists('orientationWeekCostCommitmentRequested', $opts))
      $filters[] = ['orientationWeekCostCommitmentRequested = ?', $opts['orientationWeekCostCommitmentRequested'] === 'true'];

    // Orientation week cost commitment received:
    if (array_key_exists('orientationWeekCostCommitmentReceived', $opts))
      $filters[] = ['orientationWeekCostCommitmentReceived = ?', $opts['orientationWeekCostCommitmentReceived'] === 'true'];

    // Orientation week payer:
    if (array_key_exists('orientationWeekPayer', $opts))
      $filters[] = ['orientationWeekPayer LIKE ?', '%' . $opts['orientationWeekPayer'] . '%'];

    // Orientation week remarks:
    if (array_key_exists('orientationWeekRemarks', $opts))
      $filters[] = ['orientationWeekRemarks LIKE ?', '%' . $opts['orientationWeekRemarks'] . '%'];

    // Training from:
    if (array_key_exists('trainingFrom', $opts))
      $filters[] = ['trainingFrom = ?', $opts['trainingFrom']];

    // Training to:
    if (array_key_exists('trainingTo', $opts))
      $filters[] = ['trainingTo = ?', $opts['trainingTo']];

    // Payer name:
    if (array_key_exists('payerName', $opts))
      $filters[] = ['payerName LIKE ?', '%' . $opts['payerName'] . '%'];

    // Payer address:
    if (array_key_exists('payerAddress', $opts))
      $filters[] = ['payerAddress LIKE ?', '%' . $opts['payerAddress'] . '%'];

    // Payer contact person:
    if (array_key_exists('payerContactPerson', $opts))
      $filters[] = ['payerContactPerson LIKE ?', '%' . $opts['payerContactPerson'] . '%'];

    // Payer phone:
    if (array_key_exists('payerPhone', $opts))
      $filters[] = ['payerPhone LIKE ?', '%' . $opts['payerPhone'] . '%'];

    // Payer customer number:
    if (array_key_exists('payerCustomerNumber', $opts))
      $filters[] = ['payerCustomerNumber LIKE ?', '%' . $opts['payerCustomerNumber'] . '%'];

    // Payer cost commitment:
    if (array_key_exists('payerCostCommitment', $opts))
      $filters[] = ['payerCostCommitment = ?', $opts['payerCostCommitment']];

    // Payer remarks:
    if (array_key_exists('payerRemarks', $opts))
      $filters[] = ['payerRemarks LIKE ?', '%' . $opts['payerRemarks'] . '%'];

    // Accommodation:
    if (array_key_exists('accommodation', $opts))
      $filters[] = ['accommodation = ?', $opts['accommodation'] === 'none' ? null : $opts['accommodation']];

    // Youth protection examination received:
    if (array_key_exists('youthProtectionExaminationReceived', $opts))
      $filters[] = ['youthProtectionExaminationReceived = ?', $opts['youthProtectionExaminationReceived'] === 'true'];

    return $filters;
  } // getFilters()

  public function loadEditFormRecord(\Base $f3): void {
    // Check that there are already some trainingCourses:
    if ($this->model->rel('trainingCourse1Id')->count() === 0)
      throw new ControllerException($f3->get('lng.interested.error.noTrainingCourses'));
    parent::loadEditFormRecord($f3);
  } // showEditForm()

  protected function loadLists(\Base $f3): void {
    // Load training courses:
    $list = $this->model->rel('trainingCourse1Id');
    $f3->set('page.trainingCourseList', $list->find());
    // Load marital status list
    $f3->set('page.maritalStatusList', $this->model::MARITAL_STATUS);
    // Load german level list
    $f3->set('page.germanLevelList', $this->model::GERMAN_LEVEL);
    // Load visual impairment list
    $f3->set('page.degreeOfVisualImpairmentList', $this->model::DEGREE_OF_VISUAL_IMPAIRMENT);
    // Load other disability list
    $f3->set('page.otherDisabilityList', $this->model::OTHER_DISABILITY);
    // Load tax class list
    $f3->set('page.taxClassList', $this->model::TAX_CLASS);
    // Load denomination list
    $f3->set('page.denominationList', $this->model::DENOMINATION);
    // Load payment of SV contributions list
    $f3->set('page.paymentOfSVContributionsList', $this->model::PAYMENT_OF_SV_CONTRIBUTIONS);
    // Load cost commitment list
    $f3->set('page.payerCostCommitmentList', $this->model::PAYER_COST_COMMITMENT);
    // Load accommodation list
    $f3->set('page.accommodationList', $this->model::ACCOMMODATION);
  } // loadLists()
} // class