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

  public function __construct(\AuthenticationHelper $authentication, \Model\Interested $model) {
    parent::__construct($authentication, $model);
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

  /**
   * Stream given array with rows as XLSX to the browser
   * @param Base $f3 The instance of F3
   * @param array $rows The rows to export
   * @param string $name The name of the file to stream without extension (.xlsx)
   */
  public function streamXLSX(\Base $f3, array $rows, string $name):void {
    if (empty($rows)) {
      \Flash::instance()->addMessage($f3->get('lng.interested.export.noItems'), 'info');
      $this->rerouteToLast();
    } // if
    \Spatie\SimpleExcel\SimpleExcelWriter::streamDownload($name . '.xlsx')
    ->addRows($rows)
    ->toBrowser();
  } // streamXLSX()

  /**
   * Export filtered or all interested to XLS files
   *
   * Fields are given by customer (report 1)
   */
  public function export1(\Base $f3): void {
    // Load all interested to export:
    $filter = $this->createListFilter($f3, $this->model);
    $options = $this->createListOptions($f3, $this->model);
    $this->model->load($filter, $options);
    $lng = $f3->get('lng');
    $rows = [];
    while (!$this->model->dry()) {
      $surname = $this->model->surname;
      $firstName = $this->model->firstName;
      $degreeOfVisualImpairment = $lng['interested']['degreeOfVisualImpairment'][$this->model->degreeOfVisualImpairment];
      $birthDate = $f3->format('{0,date}', strtotime($this->model->birthDate));
      $trainingCourse1 = $this->model->trainingCourse1Id->name;
      $trainingCourse2 = is_null($this->model->trainingCourse2Id) ? $lng['main']['notSet'] : $model->trainingCourse2Id->name;
      $trainingType = $lng['interested']['export'][$this->model->retraining ? 'retraining' : 'training'];
      $accommodation = $lng['interested']['accommodation'][$this->model->accommodation ?? 'none'];
      $lastStep = $this->model->getNewestStep();
      if (is_null($lastStep))
        $lastStep = $lng['interested']['export']['noSteps'];
      else
        $lastStep = $lastStep->stepTypeId->name;
      
      $rows[] = [
        $lng['interested']['fields']['surname'] => $surname,
        $lng['interested']['fields']['firstName'] => $firstName,
        $lng['interested']['fields']['degreeOfVisualImpairment'] => $degreeOfVisualImpairment,
        $lng['interested']['fields']['birthDate'] => $birthDate,
        $lng['interested']['fields']['trainingCourse1'] => $trainingCourse1,
        $lng['interested']['fields']['trainingCourse2'] => $trainingCourse2,
        $lng['interested']['export']['trainingType'] => $trainingType,
        $lng['interested']['fields']['accommodation'] => $accommodation,
        $lng['interested']['export']['lastStep'] => $lastStep,
      ];
      $this->model->next();
    } // while
    $this->streamXLSX($f3, $rows, 'Export');
  } // export1()

  /**
   * Export filtered or all interested to XLS files
   *
   * Fields are given by customer (report 2)
   */
  public function export2(\Base $f3): void {
    // Load all interested to export:
    $filter = $this->createListFilter($f3, $this->model);
    $options = $this->createListOptions($f3, $this->model);
    $this->model->load($filter, $options);
    $lng = $f3->get('lng');
    $rows = [];
    while (!$this->model->dry()) {
      $surname = $this->model->surname;
      $firstName = $this->model->firstName;
      $birthLocation = $this->model->birthLocation;
      $birthDate = $f3->format('{0,date}', strtotime($this->model->birthDate));
      $address = $this->model->address;
      $phoneMobile = $this->model->phoneMobile;
      $nationality = $this->model->nationality;
      $payerCustomerNumber = $this->model->payerCustomerNumber ?? $lng['main']['notSet'];
      $trainingFrom = $f3->format('{0,date}', strtotime($this->model->trainingFrom));
      $trainingTo = $f3->format('{0,date}', strtotime($this->model->trainingTo));
      $payerName = $this->model->payerName ?? $lng['main']['notSet'];
      $payerAddress = $this->model->payerAddress ?? $lng['main']['notSet'];
      $payerContactPerson = $this->model->payerContactPerson ?? $lng['main']['notSet'];
      $payerPhone = $this->model->payerPhone ?? $lng['main']['notSet'];
      $pensionInsuranceNumber = $this->model->pensionInsuranceNumber ?? $lng['main']['notSet'];
      $taxID = $this->model->taxID ?? $lng['main']['notSet'];
      $healthInsuranceName = $this->model->healthInsuranceName ?? $lng['main']['notSet'];
      $paymentOfSVContributions = $lng['interested']['paymentOfSVContributions'][$this->model->paymentOfSVContributions ?? 'none'];
      $handicappedIdAvailable = $lng['main'][$this->model->handicappedIdAvailable ? 'true' : 'false'];
      $trainingCourse1 = $this->model->trainingCourse1Id->name;
      $denomination = $lng['interested']['denomination'][$this->model->denomination ?? 'none'];
      $degreeOfVisualImpairment = $lng['interested']['degreeOfVisualImpairment'][$this->model->degreeOfVisualImpairment];
      $otherDisability = $lng['interested']['otherDisability'][$this->model->otherDisability ?? 'none'];

      $rows[] = [
        $lng['interested']['fields']['surname'] => $surname,
        $lng['interested']['fields']['firstName'] => $firstName,
        $lng['interested']['fields']['birthLocation'] => $birthLocation,
        $lng['interested']['fields']['birthDate'] => $birthDate,
        $lng['interested']['fields']['address'] => $address,
        $lng['interested']['fields']['phoneMobile'] => $phoneMobile,
        $lng['interested']['fields']['nationality'] => $nationality,
        $lng['interested']['fields']['payerCustomerNumber'] => $payerCustomerNumber,
        $lng['interested']['fields']['trainingCourse1'] => $trainingCourse1,
        $lng['interested']['fields']['trainingFrom'] => $trainingFrom,
        $lng['interested']['fields']['trainingTo'] => $trainingTo,
        $lng['interested']['fields']['payerName'] => $payerName,
        $lng['interested']['fields']['payerAddress'] => $payerAddress,
        $lng['interested']['fields']['payerContactPerson'] => $payerContactPerson,
        $lng['interested']['fields']['payerPhone'] => $payerPhone,
        $lng['interested']['fields']['pensionInsuranceNumber'] => $pensionInsuranceNumber,
        $lng['interested']['fields']['taxID'] => $taxID,
        $lng['interested']['fields']['healthInsuranceName'] => $healthInsuranceName,
        $lng['interested']['fields']['paymentOfSVContributions'] => $paymentOfSVContributions,
        $lng['interested']['fields']['handicappedIdAvailable'] => $handicappedIdAvailable,
        $lng['interested']['fields']['denomination'] => $denomination,
        $lng['interested']['fields']['degreeOfVisualImpairment'] => $degreeOfVisualImpairment,
        $lng['interested']['fields']['otherDisability'] => $otherDisability
      ];
      $this->model->next();
    } // while
    $this->streamXLSX($f3, $rows, 'Export');
  } // export()
} // class