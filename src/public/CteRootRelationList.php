<?php

namespace app\components\traits\sqlMakers\public;

use app\models\BaseModel;
use app\modules\api\repository\EmployeeSystemRepository;
use app\modules\command\models\DepartmentBasic;
use app\modules\employee\models\Employee;
use app\modules\employee\models\EmployeeAddress;
use app\modules\employee\models\EmployeeDepartmentMilitaryDegree;
use app\modules\employee\models\EmployeeDepartmentStaffPosition;
use app\modules\employee\models\EmployeeJeton;
use app\modules\employee\models\EmployeeMilitaryCertificate;
use app\modules\employee\models\EmployeeMilitaryDegree;
use app\modules\employee\models\EmployeePassport;
use app\modules\employee\models\EmployeePivotPassport;
use app\modules\employee\models\EmployeePunishment;
use app\modules\employee\models\EmployeeRelOrganization;
use app\modules\employee\models\EmployeeWork;
use app\modules\employee\models\EmployeeWorkQuarter;
use app\modules\employeeArchive\models\EmployeeMinistryInternalArchive;
use app\modules\manuals\models\ManualsAcademicDegree;
use app\modules\manuals\models\ManualsAcademicTitle;
use app\modules\manuals\models\ManualsAcademicTitleType;
use app\modules\manuals\models\ManualsAwardsType;
use app\modules\manuals\models\ManualsCategoryCommandType;
use app\modules\manuals\models\ManualsCitizenship;
use app\modules\manuals\models\ManualsCollateralType;
use app\modules\manuals\models\ManualsCommonCommandType;
use app\modules\manuals\models\ManualsDepartmentRelevantType;
use app\modules\manuals\models\ManualsDepartmentSocialService;
use app\modules\manuals\models\ManualsDepartmentType;
use app\modules\manuals\models\ManualsDistrict;
use app\modules\manuals\models\ManualsDoctorPositionCategoryType;
use app\modules\manuals\models\ManualsEducationalInformationType;
use app\modules\manuals\models\ManualsEducationalInstitutionType;
use app\modules\manuals\models\ManualsEducationalReadingType;
use app\modules\manuals\models\ManualsEducationCommandCourse;
use app\modules\manuals\models\ManualsEducationCommandType;
use app\modules\manuals\models\ManualsEducationInstitution;
use app\modules\manuals\models\ManualsEducationInstitutionSpecialization;
use app\modules\manuals\models\ManualsEducationInstitutionStatus;
use app\modules\manuals\models\ManualsEducationSpecializationDegree;
use app\modules\manuals\models\ManualsEmployeeActionType;
use app\modules\manuals\models\ManualsEmployeeAgeType;
use app\modules\manuals\models\ManualsEmployeeArchiveType;
use app\modules\manuals\models\ManualsEmployeeAttestation;
use app\modules\manuals\models\ManualsEmployeeCategory;
use app\modules\manuals\models\ManualsEmployeeCommandActionType;
use app\modules\manuals\models\ManualsEmployeeCommandType;
use app\modules\manuals\models\ManualsEmployeeDismissal;
use app\modules\manuals\models\ManualsEmployeeDismissalType;
use app\modules\manuals\models\ManualsEmployeeEncourageActionType;
use app\modules\manuals\models\ManualsEmployeeHealthLevelType;
use app\modules\manuals\models\ManualsEmployeeMilitaryDegreeActionType;
use app\modules\manuals\models\ManualsEmployeeMilitaryTicketType;
use app\modules\manuals\models\ManualsEmployeePermitType;
use app\modules\manuals\models\ManualsEmployeePunishment;
use app\modules\manuals\models\ManualsEmployeeReasonDeletion;
use app\modules\manuals\models\ManualsEmployeeStatesReductionType;
use app\modules\manuals\models\ManualsEmployeeType;
use app\modules\manuals\models\ManualsEmploymentContractCancellation;
use app\modules\manuals\models\ManualsEncourage;
use app\modules\manuals\models\ManualsGarrisonGuardhouse;
use app\modules\manuals\models\ManualsInjuriesType;
use app\modules\manuals\models\ManualsLaborLeaveAction;
use app\modules\manuals\models\ManualsLaborLeaveType;
use app\modules\manuals\models\ManualsMaritalStatus;
use app\modules\manuals\models\ManualsMedicalControlAccount;
use app\modules\manuals\models\ManualsMilitaryDegree;
use app\modules\manuals\models\ManualsMilitaryDegreeReason;
use app\modules\manuals\models\ManualsMilitaryDegreeStructure;
use app\modules\manuals\models\ManualsMilitaryDegreeType;
use app\modules\manuals\models\ManualsOrganizationAwards;
use app\modules\manuals\models\ManualsPercentageSurchargeType;
use app\modules\manuals\models\ManualsQuarter;
use app\modules\manuals\models\ManualsRegion;
use app\modules\manuals\models\ManualsStaffPosition;
use app\modules\manuals\models\ManualsStaffPositionCategory;
use app\modules\manuals\models\ManualsStaffPositionChief;
use app\modules\manuals\models\ManualsStaffPositionCoefficient;
use app\modules\manuals\models\ManualsStaffPositionType;
use app\modules\manuals\models\ManualsState;
use app\modules\manuals\models\ManualsStateAwards;
use app\modules\manuals\models\ManualsStateAwardsType;
use app\modules\reference\models\ReferenceBloodGroup;
use app\modules\reference\models\ReferenceCertificateSeries;
use app\modules\reference\models\ReferenceFamilyMembers;
use app\modules\reference\models\ReferenceGenders;
use app\modules\reference\models\ReferenceHealthType;
use app\modules\reference\models\ReferenceJetonSeries;
use app\modules\reference\models\ReferenceLanguages;
use app\modules\reference\models\ReferenceLanguageStatus;
use app\modules\reference\models\ReferenceNationality;
use app\modules\reference\models\ReferencePartyMembership;
use app\modules\reference\models\ReferencePassportSerial;
use app\modules\reference\models\ReferencePassportTypes;
use app\modules\structure\models\Organization;
use Exception;
use yii\db\Expression;

class CteRootRelationList
{
    /**
     * @throws Exception
     */
    public static function getRoot(): array
    {
        return [
            'unique_number' => 'employee_id',
            'select' => [
                "employee.gender_id",
                "employee.married_id",
                "employee.employee_type_id",
                "employee.system_employee_type",
                "organization_code",
                "organization_id",
                "department_position.id",
                "department_position.department_basic_id",
                "department_position.staff_posit_basic_id",
                "department_position.employee_loading_type_id",
                "system_status" => new Expression("CASE
                    WHEN employee.is_restriction = :is_restriction::bool THEN :type_1::text
                    WHEN department_position.staff_posit_basic_id IS NOT NULL AND employee.employee_status = :status_hired::integer THEN :type_2::text
                    WHEN department_position.id IS NULL AND employee.employee_status = :status_new::integer THEN :type_3::text
                    WHEN department_position.id IS NULL AND employee.employee_status = :status_no_hired::integer THEN :type_4::text
                    WHEN department_position.is_disposal = :is_disposal::boolean THEN :type_5::text
                    WHEN department_position.is_reserve = :is_reserve::boolean THEN :type_6::text
                    WHEN department_position.is_archive = :is_archive::boolean THEN :type_7::text
                    WHEN department_position.is_off = :is_off::boolean THEN :type_8::text
                    ELSE :type_9::text
                END",
                [
                    ':is_restriction' => Employee::EMPLOYEE_PERSONAL_DATA_YES_RESTRICTION,
                    ':status_no_hired' => Employee::EMPLOYEE_STATUS_NO_HIRED,
                    ':status_hired' => Employee::EMPLOYEE_STATUS_HIRED,
                    ':status_new' => Employee::EMPLOYEE_STATUS_NEW,
                    ':is_disposal' => EmployeeWork::EMPLOYEE_YES_DEPARTMENT_DISPOSAL,
                    ':is_reserve' => EmployeeWork::EMPLOYEE_YES_CURRENT_RESERVE,
                    ':is_archive' => EmployeeWork::EMPLOYEE_YES_ARCHIVE,
                    ':is_off' => EmployeeDepartmentStaffPosition::EMPLOYEE_YES_DEATH,
                    //values
                    ':type_1' => EmployeeSystemRepository::TYPE_PERMISSION,
                    ':type_2' => EmployeeSystemRepository::TYPE_ATTACHMENT,
                    ':type_3' => EmployeeSystemRepository::TYPE_NOT_CONFIRMATION,
                    ':type_4' => EmployeeSystemRepository::TYPE_NOT_ATTACHMENT,
                    ':type_5' => EmployeeSystemRepository::TYPE_DISPOSAL,
                    ':type_6' => EmployeeSystemRepository::TYPE_RESERVE,
                    ':type_7' => EmployeeSystemRepository::TYPE_ARCHIVE,
                    ':type_8' => EmployeeSystemRepository::TYPE_OFF,
                    ':type_9' => EmployeeSystemRepository::TYPE_NO_DATA,
                ]),
                "sdb.department_basic",
            ],
            'class' => EmployeeRelOrganization::class,
            'join' => [
                ['JOIN', "employee AS employee", 'on' => ["id" => "employee_id"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                ['LEFT JOIN', "employee_department_staff_position AS department_position", 'on' => ["employee_id" => "employee_id"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE, 'status_active' => BaseModel::STATUS_ACTIVE, 'current_position' => EmployeeWork::POSITION_CURRENT_ACTIVE]],
                ['LEFT JOIN', "shtat_department_basic AS sdb", 'on' => ["id" => "department_position.department_basic_id"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                ['LEFT JOIN', "reference_genders AS gender", 'on' => ["id" => "employee.gender_id"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                ['LEFT JOIN', "manuals_marital_status AS marriage", 'on' => ["id" => "employee.married_id"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
            ],
            'where' => [
                'current_organization' => BaseModel::STATUS_ACTIVE,
                'status_type' => [EmployeeMinistryInternalArchive::MINISTRY_INTERNAL_EMPLOYEE_YES_EMPLOYED, EmployeeMinistryInternalArchive::MINISTRY_INTERNAL_EMPLOYEE_ARCHIVE],
                'employee.status' => BaseModel::STATUS_ACTIVE,
            ],
            'filter' => [
                'pinfl' => "employee.jshshir",
                'organization_code' => "organization_code",
                'employee_type_code' => "employee.system_employee_type",
                'is_disposal' => "department_position.is_disposal",
                'is_reserve' => "department_position.is_reserve",
                'is_archive' => "department_position.is_archive",
                'is_off' => "department_position.is_off",
                'birth_date' => "TO_CHAR(TO_TIMESTAMP(employee.birthday), 'DD.MM.YYYY')",
                'gender_code' => "gender.code",
                'marital_status' => "marriage.code",
            ]
        ];
    }

    /**
     * @throws Exception
     */
    public static function getRelationList(): array
    {
        return [
            CteConstants::FORM_STATUS => [
                'on' => ["code" => "system_status"],
                'data' => EmployeeSystemRepository::getSystemTypeList(),
                'select' => [
                    "name_latin",
                    "name_kirill",
                    "name_qoraqalpoq",
                    "code AS system_status",
                ],
            ],
            CteConstants::FORM_PERSONAL => [
                'on' => ["id" => "unique_number"],
                'class' => Employee::class,
                'select' => [
                    "inn",
                    "jshshir",
                    "last_name AS last_name_latin",
                    "first_name AS first_name_latin",
                    "parent_name AS parent_name_latin",
                    "last_name_ru AS last_name_kirill",
                    "first_name_ru AS first_name_kirill",
                    "parent_name_ru AS parent_name_kirill",
                    "COALESCE(NULLIF(TRIM(last_name_en), ''), last_name) AS last_name_qoraqalpoq",
                    "COALESCE(NULLIF(TRIM(first_name_en), ''), first_name) AS first_name_qoraqalpoq",
                    "COALESCE(NULLIF(TRIM(parent_name_en), ''), parent_name) AS parent_name_qoraqalpoq",
                    "COALESCE(TO_CHAR(TO_TIMESTAMP(birthday), 'DD.MM.YYYY'), null) AS birth_day"
                ],
                'where' => ['status' => BaseModel::STATUS_ACTIVE]
            ],
            CteConstants::FORM_ORGANIZATION => [
                'on' => ["id" => "organization_id"],
                'class' => Organization::class,
                'select' => [
                    "name_uz AS name_latin",
                    "name_ru AS name_kirill",
                    "name_en AS name_qoraqalpoq",
                    "short_name_uz AS short_name_latin",
                    "short_name_ru AS short_name_kirill",
                    "short_name_en AS short_name_qoraqalpoq",
                    "full_name_uz AS full_name_latin",
                    "full_name_ru AS full_name_kirill",
                    "full_name_en AS full_name_qoraqalpoq",
                    "organization_type",
                    "code"
                ],
                'where' => ['status' => BaseModel::STATUS_ACTIVE]
            ],
            CteConstants::FORM_PASSPORT => [
                'on' => ["employee_id" => "unique_number"],
                'class' => EmployeePivotPassport::class,
                'select' => [
                    "ep.passport_type_uz AS passport_type",
                    "ep.jshshir",
                    "ep.passport_number",
                    "ep.given_by",
                    "COALESCE(TO_CHAR(TO_TIMESTAMP(ep.given_date), 'DD.MM.YYYY'), null) AS given_date",
                    "COALESCE(TO_CHAR(TO_TIMESTAMP(ep.validity_date), 'DD.MM.YYYY'), null) AS validity_date",
                    "rps.title AS passport_series"
                ],
                'join' => [
                    ['JOIN', "employee_passport ep", 'on' => ["id" => "employee_passport_id"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE, 'current_status' => BaseModel::STATUS_ACTIVE]],
                    ['JOIN', "reference_passport_serial rps", 'on' => ["id" => "ep.passport_series_id"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['JOIN', "reference_passport_types rpt", 'on' => ["id" => "passport_type_id"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE, 'token' => [
                        ReferencePassportTypes::TOKEN_OFP,
                        ReferencePassportTypes::TOKEN_ID_CARD,
                        ReferencePassportTypes::TOKEN_CH_F_ID_CARD,
                    ]]],
                ],
                'where' => ['status' => BaseModel::STATUS_ACTIVE]
            ],
            CteConstants::FORM_MILITARY_CERTIFICATE => [
                'on' => ["employee_id" => "unique_number"],
                'class' => EmployeeMilitaryCertificate::class,
                'select' => [
                    "rcs.code AS certificate_series",
                    "certificate_number AS certificate_number",
                    "COALESCE(TO_CHAR(TO_TIMESTAMP(begin_date), 'DD.MM.YYYY'), null) AS begin_date",
                    "COALESCE(TO_CHAR(TO_TIMESTAMP(end_date), 'DD.MM.YYYY'), null) AS end_date"
                ],
                'join' => ['JOIN', "reference_certificate_series rcs", 'on' => ["id" => "certificate_series_id"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                'where' => [
                    'status' => BaseModel::STATUS_ACTIVE,
                    'status_active' => BaseModel::STATUS_ACTIVE,
                    'current_certificate' => BaseModel::STATUS_ACTIVE
                ]
            ],
            CteConstants::FORM_MILITARY_DEGREE => [
                'on' => ["employee_id" => "unique_number"],
                'class' => EmployeeDepartmentMilitaryDegree::class,
                'select' => [
                    "mmd.id AS number",
                    "mmd.code AS code",
                    "mmd.name_uz AS name_latin",
                    "mmd.name_ru AS name_kirill",
                    "mmd.name_en AS name_qoraqalpoq",

                    "mmdt.id AS type_number",
                    "mmdt.code AS type_code",
                    "mmdt.name_uz AS type_name_latin",
                    "mmdt.name_ru AS type_name_kirill",
                    "mmdt.name_en AS type_name_qoraqalpoq",

                    "mmdr.id AS reason_number",
                    "mmdr.code AS reason_code",
                    "mmdr.name_uz AS reason_name_latin",
                    "mmdr.name_ru AS reason_name_kirill",
                    "mmdr.name_en AS reason_name_qoraqalpoq",

                    "mmds.military_degree_structure AS structure_number",
                    "mmds.code AS structure_code",
                    "mmds.name_uz AS structure_name_latin",
                    "mmds.name_ru AS structure_name_kirill",
                    "mmds.name_en AS structure_name_qoraqalpoq",

                    "COALESCE(TO_CHAR(TO_TIMESTAMP(given_date), 'DD.MM.YYYY'), null) AS begin_date"
                ],
                'join' => [
                    ['JOIN', "manuals_military_degree mmd", 'on' => ["id" => "military_degree_id"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['JOIN', "manuals_military_degree_type mmdt", 'on' => ["id" => "military_degree_type_id"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['JOIN', "manuals_military_degree_reason mmdr", 'on' => ["id" => "military_degree_reason_id"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['JOIN', "manuals_military_degree_structure mmds", 'on' => ["id" => "military_degree_structure_id"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                ],
                'where' => [
                    'status' => BaseModel::STATUS_ACTIVE,
                    'status_active' => BaseModel::STATUS_ACTIVE,
                    'current_degree' => EmployeeMilitaryDegree::MILITARY_DEGREE_CURRENT_ACTIVE,
                ]
            ],
            CteConstants::FORM_JETON => [
                'on' => ["employee_id" => "unique_number"],
                'class' => EmployeeJeton::class,
                'select' => [
                    "rjs.code AS jeton_series",
                    "jeton_number AS jeton_number",
                    "COALESCE(TO_CHAR(TO_TIMESTAMP(begin_date), 'DD.MM.YYYY'), null) AS begin_date"
                ],
                'join' => ['JOIN', "reference_jeton_series rjs", 'on' => ["id" => "jeton_series_id"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                'where' => [
                    'status' => BaseModel::STATUS_ACTIVE,
                    'status_active' => BaseModel::STATUS_ACTIVE,
                    'status_command' => BaseModel::STATUS_ACTIVE,
                ]
            ],
            CteConstants::FORM_CITIZENSHIP => [
                'on' => ["unique_number" => "unique_number"],
                "unique_number" => "mc.id",
                'with' => 'cte_address',
                'select' => [
                    "mc.name_uz AS name_latin",
                    "mc.name_ru AS name_kirill",
                    "mc.name_en AS name_qoraqalpoq",
                    "mc.code"
                ],
                'join' => ['JOIN', "manuals_citizenship mc", 'on' => ["id" => "citizenship_id"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
            ],
            CteConstants::FORM_NATIONALITY => [
                'on' => ["unique_number" => "unique_number"],
                "unique_number" => "rn.id",
                'with' => 'cte_address',
                'select' => [
                    "rn.name_uz AS name_latin",
                    "rn.name_ru AS name_kirill",
                    "rn.name_en AS name_qoraqalpoq",
                    "rn.code"
                ],
                'join' => ['JOIN', "reference_nationality rn", 'on' => ["id" => "nationality_id"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
            ],
            CteConstants::FORM_MARITAL_STATUS => [
                'on' => ["id" => "married_id"],
                'class' => ManualsMaritalStatus::class,
                'select' => [
                    "name_uz AS name_latin",
                    "name_ru AS name_kirill",
                    "name_en AS name_qoraqalpoq",
                    "code"
                ],
                'where' => ['status' => BaseModel::STATUS_ACTIVE]
            ],
            CteConstants::FORM_GENDER => [
                'on' => ["id" => "gender_id"],
                'class' => ReferenceGenders::class,
                'select' => [
                    "name_uz AS name_latin",
                    "name_ru AS name_kirill",
                    "name_en AS name_qoraqalpoq",
                    "code"
                ],
                'where' => ['status' => BaseModel::STATUS_ACTIVE]
            ],
            CteConstants::FORM_EMPLOYEE_TYPE => [
                'on' => ["id" => "employee_type_id"],
                'class' => ManualsEmployeeType::class,
                'select' => [
                    "short_name_uz AS short_name_latin",
                    "short_name_ru AS short_name_kirill",
                    "short_name_en AS short_name_qoraqalpoq",
                    "name_uz AS name_latin",
                    "name_ru AS name_kirill",
                    "name_en AS name_qoraqalpoq",
                    "code"
                ],
                'where' => ['status' => BaseModel::STATUS_ACTIVE]
            ],
            CteConstants::FORM_PASSPORT_PHOTO => [
                'on' => ["root_number" => "unique_number"],
                'unique_number' => 'unique_number',
                'table' => CteConstants::FORM_PASSPORT,
                'select' => [
                    "ea.absolute_path AS photo"
                ],
                'join' => [
                    ['JOIN', "employee_passport_attachment epa", 'on' => ["employee_pivot_passport_id" => "unique_number"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['JOIN', "employee_attachment ea", 'on' => ["id" => "epa.employee_attachment_id"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                ],
            ],
            CteConstants::FORM_MILITARY_PHOTO => [
                'on' => ["root_number" => "unique_number"],
                'unique_number' => 'unique_number',
                'table' => CteConstants::FORM_MILITARY_CERTIFICATE,
                'select' => [
                    "ea.absolute_path AS photo"
                ],
                'join' => [
                    ['JOIN', "employee_certificate_attachment eca", 'on' => "eca.military_certificate_id = ".CteConstants::FORM_MILITARY_PHOTO.".unique_number OR eca.employee_id = ".CteConstants::CTE_ROOT_LIMITED.".unique_number", 'condition' => ['status' => BaseModel::STATUS_ACTIVE, 'current_status' => BaseModel::STATUS_ACTIVE]],
                    ['JOIN', "employee_attachment ea", 'on' => ["id" => "eca.employee_attachment_id"], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                ],
            ],
            CteConstants::FORM_POSITION_TYPE => [
                'on' => ["id" => "employee_loading_type_id"],
                'class' => ManualsEmploymentContractCancellation::class,
                'select' => [
                    "code",
                    "contract_type",
                    "name_uz AS name_latin",
                    "name_ru AS name_kirill",
                    "name_en AS name_qoraqalpoq"
                ],
            ],
            CteConstants::FORM_PUNISHMENT => [
                'on' => ["employee_id" => "unique_number"],
                'class' => EmployeePunishment::class,
                'select' => [
                    "organization_name_uz AS command_name_latin",
                    "organization_name_ru AS command_name_kirill",
                    "organization_name_en AS command_name_qoraqalpoq",
                    "command_number",
                    "COALESCE(TO_CHAR(TO_TIMESTAMP(command_date), 'DD.MM.YYYY'), null) AS command_date",
                    "COALESCE(TO_CHAR(TO_TIMESTAMP(begin_date), 'DD.MM.YYYY'), null) AS begin_date",
                    "mep.name_uz AS name_latin",
                    "mep.name_ru AS name_kirill",
                    "mep.name_en AS name_qoraqalpoq",
                    "mep.code"
                ],
                'join' => ['LEFT JOIN', 'manuals_employee_punishment mep', 'on' => ['id' => 'punishment_type_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                'where' => [
                    'status' => BaseModel::STATUS_ACTIVE,
                    'status_active' => BaseModel::STATUS_ACTIVE,
                    'is_command_punishment' => EmployeePunishment::EMPLOYEE_NO_COMMAND_PUNISHMENT,
                    'is_high_punishment' => EmployeePunishment::EMPLOYEE_NO_HIGH_PUNISHMENT,
                    'is_punishment' => EmployeePunishment::EMPLOYEE_NO_PUNISHMENT,
                ]
            ],
            CteConstants::FORM_INSPECTOR_QUARTER => [
                'on' => ["department_staff_position_id" => "id"],
                'class' => EmployeeWorkQuarter::class,
                'select' => [
                    "mr.code AS region_avir_code",
                    "mr.coato_code AS region_coato_code",
                    "mr.service_code AS region_service_code",
                    "mr.name_uz AS region_name_latin",
                    "mr.name_ru AS region_name_kirill",
                    "COALESCE(NULLIF(mr.name_en, ''), mr.name_ru) AS region_name_qoraqalpoq",

                    "md.code AS district_avir_code",
                    "md.coato_code AS district_coato_code",
                    "md.service_code AS district_service_code",
                    "md.name_uz AS district_name_latin",
                    "md.name_ru AS district_name_kirill",
                    "COALESCE(NULLIF(md.name_en, ''), md.name_ru) AS district_name_qoraqalpoq",

                    "mq.population_count AS quarter_population_count",
                    "mq.sector_code AS quarter_sector_code",
                    "mq.color_code AS quarter_color_code",
                    "mq.coato_code AS quarter_coato_code",
                    "mq.service_code AS quarter_service_code",
                    "mq.name_uz AS quarter_name_latin",
                    "mq.name_ru AS quarter_name_kirill",
                    "COALESCE(NULLIF(mq.name_en, ''), mq.name_ru) AS quarter_name_qoraqalpoq"
                ],
                'join' => [
                    ['LEFT JOIN', "manuals_region mr", 'on' => ['id' => 'region_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['LEFT JOIN', "manuals_district md", 'on' => ['id' => 'district_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['LEFT JOIN', "manuals_quarter mq", 'on' => ['id' => 'quarter_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                ],
                'where' => ['status' => BaseModel::STATUS_ACTIVE, 'is_current' => EmployeeWorkQuarter::EMPLOYEE_QUARTER_YES_CURRENT]
            ],
            CteConstants::FORM_BIRTH_ADDRESS => [
                'on' => ["unique_number" => "unique_number"],
                "unique_number" => "unique_number",
                'with' => "cte_address",
                'select' => [
                    "CONCAT('{uz}', birth_address) AS address_latin",
                    "CONCAT('{ru}', birth_address) AS address_kirill",
                    "CONCAT('{en}', birth_address) AS address_qoraqalpoq",

                    "ms.id AS state_id",
                    "ms.code AS state_avir_code",
                    "ms.coato_code AS state_coato_code",
                    "ms.name_uz AS state_name_latin",
                    "ms.name_ru AS state_name_kirill",
                    "ms.name_en AS state_name_qoraqalpoq",

                    "mr.id AS region_id",
                    "mr.code AS region_avir_code",
                    "mr.coato_code AS region_coato_code",
                    "mr.name_uz AS region_name_latin",
                    "mr.name_ru AS region_name_kirill",
                    "mr.name_en AS region_name_qoraqalpoq",

                    "md.id AS district_id",
                    "md.code AS district_avir_code",
                    "md.coato_code AS district_coato_code",
                    "md.name_uz AS district_name_latin",
                    "md.name_ru AS district_name_kirill",
                    "md.name_en AS district_name_qoraqalpoq",

                    "mq.id AS quarter_id",
                    "mq.coato_code AS quarter_coato_code",
                    "mq.sector_code AS quarter_sector_code",
                    "mq.service_code AS quarter_service_code",
                    "mq.name_uz AS quarter_name_latin",
                    "mq.name_ru AS quarter_name_kirill",
                    "mq.name_en AS quarter_name_qoraqalpoq"
                ],
                'join' => [
                    ['LEFT JOIN', "manuals_state ms", 'on' => ['id' => 'birth_state_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['LEFT JOIN', "manuals_region mr", 'on' => ['id' => 'birth_region_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['LEFT JOIN', "manuals_district md", 'on' => ['id' => 'birth_district_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['LEFT JOIN', "manuals_quarter mq", 'on' => ['id' => 'birth_quarter_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                ]
            ],
            CteConstants::FORM_CONSTANT_ADDRESS => [
                'on' => ["unique_number" => "unique_number"],
                "unique_number" => "unique_number",
                'with' => "cte_address",
                'select' => [
                    "CONCAT('{uz}', constant_address) AS address_latin",
                    "CONCAT('{ru}', constant_address) AS address_kirill",
                    "CONCAT('{en}', constant_address) AS address_qoraqalpoq",

                    "mr.id AS region_id",
                    "mr.code AS region_avir_code",
                    "mr.coato_code AS region_coato_code",
                    "mr.name_uz AS region_name_latin",
                    "mr.name_ru AS region_name_kirill",
                    "mr.name_en AS region_name_qoraqalpoq",

                    "md.id AS district_id",
                    "md.code AS district_avir_code",
                    "md.coato_code AS district_coato_code",
                    "md.name_uz AS district_name_latin",
                    "md.name_ru AS district_name_kirill",
                    "md.name_en AS district_name_qoraqalpoq",

                    "mq.id AS quarter_id",
                    "mq.coato_code AS quarter_coato_code",
                    "mq.sector_code AS quarter_sector_code",
                    "mq.service_code AS quarter_service_code",
                    "mq.name_uz AS quarter_name_latin",
                    "mq.name_ru AS quarter_name_kirill",
                    "mq.name_en AS quarter_name_qoraqalpoq"
                ],
                'join' => [
                    ['LEFT JOIN', "manuals_region mr", 'on' => ['id' => 'constant_region_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['LEFT JOIN', "manuals_district md", 'on' => ['id' => 'constant_district_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['LEFT JOIN', "manuals_quarter mq", 'on' => ['id' => 'constant_quarter_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                ]
            ],
            CteConstants::FORM_CURRENT_ADDRESS => [
                'on' => ["unique_number" => "unique_number"],
                "unique_number" => "unique_number",
                'with' => "cte_address",
                'select' => [
                    "CONCAT('{uz}', current_address) AS address_latin",
                    "CONCAT('{ru}', current_address) AS address_kirill",
                    "CONCAT('{en}', current_address) AS address_qoraqalpoq",

                    "mr.id AS region_id",
                    "mr.code AS region_avir_code",
                    "mr.coato_code AS region_coato_code",
                    "mr.name_uz AS region_name_latin",
                    "mr.name_ru AS region_name_kirill",
                    "mr.name_en AS region_name_qoraqalpoq",

                    "md.id AS district_id",
                    "md.code AS district_avir_code",
                    "md.coato_code AS district_coato_code",
                    "md.name_uz AS district_name_latin",
                    "md.name_ru AS district_name_kirill",
                    "md.name_en AS district_name_qoraqalpoq",

                    "mq.id AS quarter_id",
                    "mq.coato_code AS quarter_coato_code",
                    "mq.sector_code AS quarter_sector_code",
                    "mq.service_code AS quarter_service_code",
                    "mq.name_uz AS quarter_name_latin",
                    "mq.name_ru AS quarter_name_kirill",
                    "mq.name_en AS quarter_name_qoraqalpoq"
                ],
                'join' => [
                    ['LEFT JOIN', "manuals_region mr", 'on' => ['id' => 'current_region_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['LEFT JOIN', "manuals_district md", 'on' => ['id' => 'current_district_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['LEFT JOIN', "manuals_quarter mq", 'on' => ['id' => 'current_quarter_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                ]
            ],
            CteConstants::FORM_POSITION => [
                'on' => ["unique_number" => "unique_number"],
                "unique_number" => "unique_number",
                'with' => "cte_positionBasic",
                'select' => [
                    "code",
                    "full_code",
                    "first_begin_date",
                    "current_begin_date",
                    "name_uz AS name_latin",
                    "name_ru AS name_kirill",
                    "COALESCE(NULLIF(name_en, ''), name_uz) AS name_qoraqalpoq",
                    "COALESCE(NULLIF(CONCAT(loading_uz, position_name_uz, replacement_uz, reduction_uz, collateral_uz, quarter_uz), ''), name_uz) AS full_name_latin",
                    "COALESCE(NULLIF(CONCAT(loading_ru, position_name_ru, replacement_ru, reduction_ru, collateral_ru, quarter_ru), ''), name_ru) AS full_name_kirill",
                    "COALESCE(NULLIF(CONCAT(loading_en, position_name_en, replacement_en, reduction_en, collateral_en, quarter_en), ''), name_en) AS full_name_qoraqalpoq"
                ]
            ],
            CteConstants::FORM_DEPARTMENT => [
                'on' => ["department_basic" => "department_basic"],
                'recursive' => ['department_basic' => 'parent_id'],
                'class' => DepartmentBasic::class,
                'select' => [
                    "department_inn",
                    "CONCAT('department_basic_', department_basic, '_code') AS code",
                    "name_uz AS name_latin",
                    "name_ru AS name_kirill",
                    "name_en AS name_qoraqalpoq",
                    "RECURSIVE(name_uz) AS full_name_latin",
                    "RECURSIVE(name_ru) AS full_name_kirill",
                    "COALESCE(NULLIF(TRIM(RECURSIVE(name_en)), ''), RECURSIVE(name_uz)) AS full_name_qoraqalpoq"
                ],
                'where' => [
                    'status' => BaseModel::STATUS_ACTIVE,
                    'check_view' => DepartmentBasic::DEPARTMENT_CHECK_VIEW_YES,
                ],
            ],
        ];
    }
    /**
     * Qo‘shimcha yordamchi CTE lar - "chop etilish ro‘yxatida chiqmaydi"
     * @throws Exception
     */

    public static function getWithList(): array
    {
        return [
            "cte_address" => [
                'on' => ["employee_id" => "unique_number"],
                "unique_number" => "employee_id",
                'class' => EmployeeAddress::class,
                'select' => [
                    'citizenship_id',
                    'nationality_id',

                    "birth_state_id",
                    "birth_region_id",
                    "birth_district_id",
                    "birth_quarter_id",
                    "birth_address",

                    "constant_region_id",
                    "constant_district_id",
                    "constant_quarter_id",
                    "constant_address",

                    "current_region_id",
                    "current_district_id",
                    "current_quarter_id",
                    "current_address",
                ],
                'where' => ['status' => BaseModel::STATUS_ACTIVE],
            ],
            'cte_positionBasic' => [
                'on' => ["id" => "id"],
                "unique_number" => "employee_id",
                'class' => EmployeeDepartmentStaffPosition::class,
                'select' => [
                    'code' => "msp.code",
                    'full_code' => new Expression("CONCAT('staff_position_basic_', sspb.staff_position_basic, '_code')"),
                    'first_begin_date' => new Expression("COALESCE(TO_CHAR(TO_TIMESTAMP(ewf.begin_date), 'DD.MM.YYYY'), null)"),
                    'current_begin_date' => new Expression("COALESCE(TO_CHAR(TO_TIMESTAMP(staff_position_start_time), 'DD.MM.YYYY'), null)"),

                    'name_uz' => new Expression("CASE 
                            WHEN is_disposal = true THEN 'Boshqarma ixtiyorida'
                            WHEN is_reserve = true THEN 'Kadrlarning amaldagi zaxirasida'
                            WHEN is_archive = true THEN 'Bekor qilingan'
                            ELSE COALESCE(msp.name_uz, ew.staff_position_name_uz)
                        END"),
                    'name_ru' => new Expression("CASE 
                            WHEN is_disposal = true THEN 'Бошқарма ихтиёрида'
                            WHEN is_reserve = true THEN 'Кадрларнинг амалдаги захирасида'
                            WHEN is_archive = true THEN 'Бекор қилинган'
                            ELSE COALESCE(msp.name_ru, ew.staff_position_name_ru)
                        END"),
                    'name_en' => new Expression("CASE 
                            WHEN is_disposal = true THEN 'Basqarma ixtiyorida'
                            WHEN is_reserve = true THEN 'Kadrlardıń ámeldegi rezervinde'
                            WHEN is_archive = true THEN 'Biykar etilgen'
                            ELSE COALESCE(COALESCE(msp.name_en, ew.staff_position_name_en), COALESCE(msp.name_ru, ew.staff_position_name_ru))
                        END"),

                    'position_name_uz' => new Expression("CASE
                            WHEN sspb.id IS NOT NULL THEN
                                CASE 
                                    WHEN ".CteConstants::CTE_ROOT_LIMITED.".system_employee_type IN (:system_worker::text, :system_servant::text) AND mspt.code IN (:position_special::text, :position_military::text) THEN 
                                        CASE 
                                            WHEN is_position_behalf = true THEN CONCAT(position_behalf_name_uz, ' (shahodatlangan ', msp.name_uz, ' lavozimi hisobidan)')
                                            WHEN employee_acting = :status THEN CONCAT('shahodatlangan ', msp.name_uz, ' (vazifasini vaqtincha bajaruvchisi)')
                                            ELSE CONCAT('shahodatlangan ', msp.name_uz)
                                        END
                                    ELSE 
                                        CASE 
                                            WHEN is_position_behalf = true THEN CONCAT(position_behalf_name_uz, ' (', msp.name_uz, ' lavozimi hisobidan)')
                                            WHEN employee_acting = :status THEN CONCAT(msp.name_uz, ' (vazifasini vaqtincha bajaruvchisi)')
                                            ELSE msp.name_uz
                                        END
                                END
                            WHEN COALESCE(ew.staff_position_name_uz, '') <> '' THEN
                                CASE 
                                    WHEN is_position_behalf = true THEN CONCAT(position_behalf_name_uz, ' (', ew.staff_position_name_uz, ' lavozimi hisobidan)')
                                    WHEN employee_acting = :status THEN CONCAT(ew.staff_position_name_uz, ' (vazifasini vaqtincha bajaruvchisi)')
                                    ELSE ew.staff_position_name_uz
                                END
                            ELSE ew.department_name_uz
                        END", [
                            ':status' => BaseModel::STATUS_ACTIVE,
                            ':system_worker' => ManualsEmployeeType::EMPLOYEE_SYSTEM_TYPE_WORKER,
                            ':system_servant' => ManualsEmployeeType::EMPLOYEE_SYSTEM_TYPE_SERVANT,
                            ':position_special' => ManualsStaffPositionType::STAFF_POSITION_SPECIAL,
                            ':position_military' => ManualsStaffPositionType::STAFF_POSITION_MILITARY,
                        ]),
                    'position_name_ru' => new Expression("CASE
                            WHEN sspb.id IS NOT NULL THEN
                                CASE 
                                    WHEN ".CteConstants::CTE_ROOT_LIMITED.".system_employee_type IN (:system_worker::text, :system_servant::text) AND mspt.code IN (:position_special::text, :position_military::text) THEN 
                                        CASE 
                                            WHEN is_position_behalf = true THEN CONCAT(position_behalf_name_ru, ' (шаҳодатланган ', msp.name_ru, ' лавозими ҳисобидан)')
                                            WHEN employee_acting = :status THEN CONCAT('шаҳодатланган ', msp.name_ru, ' (вазифасини вақтинча бажарувчиси)')
                                            ELSE CONCAT('шаҳодатланган ', msp.name_ru)
                                        END
                                    ELSE 
                                        CASE 
                                            WHEN is_position_behalf = true THEN CONCAT(position_behalf_name_ru, ' (', msp.name_ru, ' лавозими ҳисобидан)')
                                            WHEN employee_acting = :status THEN CONCAT(msp.name_ru, ' (вазифасини вақтинча бажарувчиси)')
                                            ELSE msp.name_ru
                                        END
                                END
                            WHEN COALESCE(ew.staff_position_name_ru, '') <> '' THEN
                                CASE 
                                    WHEN is_position_behalf = true THEN CONCAT(position_behalf_name_ru, ' (', ew.staff_position_name_ru, ' лавозими ҳисобидан)')
                                    WHEN employee_acting = :status THEN CONCAT(ew.staff_position_name_ru, ' (вазифасини вақтинча бажарувчиси)')
                                    ELSE ew.staff_position_name_ru
                                END
                            ELSE ew.department_name_ru
                        END", [
                            ':status' => BaseModel::STATUS_ACTIVE,
                            ':system_worker' => ManualsEmployeeType::EMPLOYEE_SYSTEM_TYPE_WORKER,
                            ':system_servant' => ManualsEmployeeType::EMPLOYEE_SYSTEM_TYPE_SERVANT,
                            ':position_special' => ManualsStaffPositionType::STAFF_POSITION_SPECIAL,
                            ':position_military' => ManualsStaffPositionType::STAFF_POSITION_MILITARY,
                        ]),
                    'position_name_en' => new Expression("CASE
                            WHEN sspb.id IS NOT NULL THEN
                                CASE 
                                    WHEN ".CteConstants::CTE_ROOT_LIMITED.".system_employee_type IN (:system_worker::text, :system_servant::text) AND mspt.code IN (:position_special::text, :position_military::text) THEN 
                                        CASE 
                                            WHEN is_position_behalf = true THEN CONCAT(COALESCE(NULLIF(position_behalf_name_en, ''), position_behalf_name_ru), ' (gúwalanǵan ', msp.name_ru, ' лавозими ҳисобидан)')
                                            WHEN employee_acting = :status THEN CONCAT('gúwalanǵan ', msp.name_ru, ' (вазифасини вақтинча бажарувчиси)')
                                            ELSE CONCAT('gúwalanǵan ', msp.name_ru)
                                        END
                                    ELSE 
                                        CASE 
                                            WHEN is_position_behalf = true THEN CONCAT(COALESCE(NULLIF(position_behalf_name_en, ''), position_behalf_name_ru), ' (', msp.name_ru, ' лавозими ҳисобидан)')
                                            WHEN employee_acting = :status THEN CONCAT(msp.name_ru, ' (вазифасини вақтинча бажарувчиси)')
                                            ELSE msp.name_ru
                                        END
                                END
                            WHEN COALESCE(ew.staff_position_name_ru, '') <> '' THEN
                                CASE 
                                    WHEN is_position_behalf = true THEN CONCAT(COALESCE(NULLIF(position_behalf_name_en, ''), position_behalf_name_ru), ' (', ew.staff_position_name_ru, ' лавозими ҳисобидан)')
                                    WHEN employee_acting = :status THEN CONCAT(ew.staff_position_name_ru, ' (вазифасини вақтинча бажарувчиси)')
                                    ELSE ew.staff_position_name_ru
                                END
                            ELSE COALESCE(NULLIF(ew.department_name_en, ''), ew.department_name_ru)
                        END", [
                            ':status' => BaseModel::STATUS_ACTIVE,
                            ':system_worker' => ManualsEmployeeType::EMPLOYEE_SYSTEM_TYPE_WORKER,
                            ':system_servant' => ManualsEmployeeType::EMPLOYEE_SYSTEM_TYPE_SERVANT,
                            ':position_special' => ManualsStaffPositionType::STAFF_POSITION_SPECIAL,
                            ':position_military' => ManualsStaffPositionType::STAFF_POSITION_MILITARY,
                        ]),

                    'loading_uz' => new Expression("CASE WHEN mecc.id IS NOT NULL AND staff_count > 0 AND staff_count <> 1 THEN
                            CASE
                                WHEN mecc.contract_type = :loading_type AND mecc.code = :loading_code THEN CONCAT('(',LOWER(mecc.name_uz), ' ', staff_count, ' stavka) ')
                                ELSE CONCAT('(',staff_count, ' stavka) ')
                            END
                        END", [':loading_type' => ManualsEmploymentContractCancellation::CONTRACT_TYPE_LOADING, ':loading_code' => ManualsEmploymentContractCancellation::EMPLOYEE_LOADING_EXTERNAL]),
                    'loading_ru' => new Expression("CASE WHEN mecc.id IS NOT NULL AND staff_count > 0 AND staff_count <> 1 THEN
                            CASE
                                WHEN mecc.contract_type = :loading_type AND mecc.code = :loading_code THEN CONCAT('(',LOWER(mecc.name_ru), ' ', staff_count, ' ставка) ')
                                ELSE CONCAT('(',staff_count, ' ставка) ')
                            END
                        END", [':loading_type' => ManualsEmploymentContractCancellation::CONTRACT_TYPE_LOADING, ':loading_code' => ManualsEmploymentContractCancellation::EMPLOYEE_LOADING_EXTERNAL]),
                    'loading_en' => new Expression("CASE WHEN mecc.id IS NOT NULL AND staff_count > 0 AND staff_count <> 1 THEN
                            CASE
                                WHEN mecc.contract_type = :loading_type AND mecc.code = :loading_code THEN CONCAT('(',LOWER(mecc.name_en), ' ', staff_count, ' stavka) ')
                                ELSE CONCAT('(',staff_count, ' stavka) ')
                            END
                        END", [':loading_type' => ManualsEmploymentContractCancellation::CONTRACT_TYPE_LOADING, ':loading_code' => ManualsEmploymentContractCancellation::EMPLOYEE_LOADING_EXTERNAL]),

                    'replacement_uz' => new Expression("CASE WHEN is_replacement = true THEN ' (rotatsiya tartibida)' END"),
                    'replacement_ru' => new Expression("CASE WHEN is_replacement = true THEN ' (ротация тартибида)' END"),
                    'replacement_en' => new Expression("CASE WHEN is_replacement = true THEN ' (rotatsiya tártibinde)' END"),

                    'reduction_uz' => new Expression("CASE WHEN is_states_reduction = true AND mesrt.id IS NOT NULL THEN CONCAT(' (', mesrt.name_uz, ')') END"),
                    'reduction_ru' => new Expression("CASE WHEN is_states_reduction = true AND mesrt.id IS NOT NULL THEN CONCAT(' (', mesrt.name_ru, ')') END"),
                    'reduction_en' => new Expression("CASE WHEN is_states_reduction = true AND mesrt.id IS NOT NULL THEN CONCAT(' (', mesrt.name_en, ')') END"),

                    'collateral_uz' => new Expression("CASE 
                            WHEN is_collateral = true AND mct.id IS NOT NULL THEN CONCAT(' (',mct.name_uz,')') 
                            WHEN is_collateral = true AND mct.id IS NOT NULL AND COALESCE(collateral_comment_uz, '') <> '' THEN CONCAT(' (',mct.name_uz,' - ', collateral_comment_uz, ')') 
                        END"),
                    'collateral_ru' => new Expression("CASE 
                            WHEN is_collateral = true AND mct.id IS NOT NULL THEN CONCAT(' (',mct.name_ru,')') 
                            WHEN is_collateral = true AND mct.id IS NOT NULL AND COALESCE(collateral_comment_ru, '') <> '' THEN CONCAT(' (',mct.name_ru,' - ', collateral_comment_ru, ')') 
                        END"),
                    'collateral_en' => new Expression("CASE 
                            WHEN is_collateral = true AND mct.id IS NOT NULL THEN CONCAT(' (',COALESCE(NULLIF(mct.name_en, ''), mct.name_uz),')') 
                            WHEN is_collateral = true AND mct.id IS NOT NULL AND COALESCE(COALESCE(NULLIF(collateral_comment_en, ''), collateral_comment_uz), '') <> '' THEN CONCAT(' (',COALESCE(NULLIF(mct.name_en, ''), mct.name_uz),' - ', COALESCE(NULLIF(collateral_comment_en, ''), collateral_comment_uz), ')') 
                        END"),

                    'quarter_uz' => new Expression("CASE WHEN is_quarter = true AND ewq.id IS NOT NULL THEN CONCAT(' (', ewq.quarter_name_uz, ', aholi soni ', ewq.population_count, ' nafar, ',
                            (CASE 
                                WHEN ewq.color_code = 'yellow' THEN 'sariq'
                                WHEN ewq.color_code = 'green' THEN 'yashil'
                                WHEN ewq.color_code = 'red' THEN 'qizil'
                            END), ' hudud, ', ewq.sector_code, '-sektor)') 
                        END"),
                    'quarter_ru' => new Expression("CASE WHEN is_quarter = true AND ewq.id IS NOT NULL THEN CONCAT(' (', ewq.quarter_name_ru, ', аҳоли сони ', ewq.population_count, ' нафар, ',
                            (CASE 
                                WHEN ewq.color_code = 'yellow' THEN 'сариқ'
                                WHEN ewq.color_code = 'green' THEN 'яшил'
                                WHEN ewq.color_code = 'red' THEN 'қизил'
                            END), ' ҳудуд, ', ewq.sector_code, '-сектор)') 
                        END"),
                    'quarter_en' => ("CASE WHEN is_quarter = true AND ewq.id IS NOT NULL THEN CONCAT(' (', COALESCE(NULLIF(ewq.quarter_name_en, ''), ewq.quarter_name_uz), ', xalıq sanı ', ewq.population_count, ' dana, ',
                            (CASE 
                                WHEN ewq.color_code = 'yellow' THEN 'sarı'
                                WHEN ewq.color_code = 'green' THEN 'jasıl'
                                WHEN ewq.color_code = 'red' THEN 'qızıl'
                            END), ' aymaq , ', ewq.sector_code, '-sektor)') 
                        END")
                ],
                'join' => [
                    ['LEFT JOIN', "employee_work ew", 'on' => ['department_staff_position_id' => 'id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['LEFT JOIN', "employee_work ewf", 'on' => ['employee_id' => 'employee_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE, 'status_active' => BaseModel::STATUS_ACTIVE, 'is_first_labor_activity' => EmployeeWork::EMPLOYEE_YES_FIRST_LABOR_ACTIVITY]],
                    ['LEFT JOIN', "shtat_staff_position_basic sspb", 'on' => ['id' => 'staff_posit_basic_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['LEFT JOIN', "manuals_staff_position msp", 'on' => ['id' => 'sspb.staff_position_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['LEFT JOIN', "manuals_staff_position_type mspt", 'on' => ['id' => 'sspb.staff_position_type_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['LEFT JOIN', "manuals_employee_states_reduction_type mesrt", 'on' => ['id' => 'states_reduction_type_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['LEFT JOIN', "manuals_collateral_type mct", 'on' => ['id' => 'collateral_type_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['LEFT JOIN', "manuals_employment_contract_cancellation mecc", 'on' => ['id' => 'employee_loading_type_id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE]],
                    ['LEFT JOIN', "employee_work_quarter ewq", 'on' => ['department_staff_position_id' => 'id'], 'condition' => ['status' => BaseModel::STATUS_ACTIVE, 'is_current' => EmployeeWorkQuarter::EMPLOYEE_QUARTER_YES_CURRENT]],
                ]
            ],
        ];
    }
}