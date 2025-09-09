<?php

namespace app\components\traits\sqlMakers\public;

use app\modules\api\repository\EmployeeSystemRepository;
use app\modules\command\models\DepartmentBasic;
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

class CteReferenceList
{
    /**
     * @throws Exception
     */
    public static function getReferenceList(): array
    {
        return [
                CteConstants::REF_EMPLOYEE_SYSTEM_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_latin",
                        "name_kirill",
                        "name_qoraqalpoq",
                        "status"
                    ],
                    'data' => EmployeeSystemRepository::getSystemTypeList(),
                ],
                CteConstants::REF_ORGANIZATION => [
                    'select' => [
                        "code AS unique_code",
                        'parent.code AS parent_code',
                        "organization_type",
                        "is_basic_organization",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "short_name_uz AS short_name_latin",
                        "short_name_ru AS short_name_kirill",
                        "short_name_en AS short_name_qoraqalpoq",
                        "order_number",
                        "status"
                    ],
                    'class' => Organization::class,
                    'join' => ['LEFT JOIN', "structure_organization AS parent", 'on' => ["id" => "parent_id"]],
                ],
                CteConstants::REF_DEPARTMENT => [
                    'unique_number' => 'department_basic',
                    'select' => [
                        'unique_code' => new Expression("CONCAT('department_basic_', department_basic, '_code')"),
                        'parent_code' => new Expression("CASE WHEN parent.department_basic IS NOT NULL THEN CONCAT('department_basic_', parent.department_basic, '_code') END"),
                        'inn' => new Expression("CASE WHEN is_inn IS TRUE THEN department_inn END"),
                        'name_latin' => "name_uz",
                        'name_kirill' => "name_ru",
                        'name_qoraqalpoq' => new Expression("COALESCE(NULLIF(TRIM(name_en), ''), name_uz)"),
                        'additional_latin' => new Expression("NULLIF(TRIM(additional_uz), '')"),
                        'additional_kirill' => new Expression("NULLIF(TRIM(additional_ru), '')"),
                        'additional_qoraqalpoq' => new Expression("NULLIF(TRIM(additional_en), '')"),
                        'comment_latin' => new Expression("CASE WHEN is_comment IS TRUE THEN comment_uz END"),
                        'comment_kirill' => new Expression("CASE WHEN is_comment IS TRUE THEN comment_ru END"),
                        'comment_qoraqalpoq' => new Expression("CASE WHEN is_comment IS TRUE THEN comment_en END"),
                        'order_number' => "order_number",
                        "status",
                        'ref_department_type_unique_code' => CteConstants::REF_DEPARTMENT_TYPE.".unique_code",
                        'ref_department_relevant_type_unique_code' => CteConstants::REF_DEPARTMENT_RELEVANT_TYPE.".unique_code",
                        'ref_department_social_service_unique_code' => CteConstants::REF_DEPARTMENT_SOCIAL_SERVICE.".unique_code",
                    ],
                    'class' => DepartmentBasic::class,
                    'join' => [
                        ['LEFT JOIN', "shtat_department_basic AS parent", 'on' => ["department_basic" => "parent_id"]],
                        ['LEFT JOIN', CteConstants::REF_DEPARTMENT_TYPE, 'on' => ["unique_number" => "department_type_id"]],
                        ['LEFT JOIN', CteConstants::REF_DEPARTMENT_RELEVANT_TYPE, 'on' => ["unique_number" => "department_relevant_type_id"]],
                        ['LEFT JOIN', CteConstants::REF_DEPARTMENT_SOCIAL_SERVICE, 'on' => ["unique_number" => "department_social_service_id"]],
                    ],
                    'where' => ['is_another_department' => false, 'course_stage' => null, 'course_code' => null],
                ],
                CteConstants::REF_DEPARTMENT_TYPE => [
                    'unique_number' => 'department_type',
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsDepartmentType::class,
                ],
                CteConstants::REF_DEPARTMENT_RELEVANT_TYPE => [
                    'unique_number' => 'department_relevant_type',
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsDepartmentRelevantType::class,
                    'where' => ['is_position' => ManualsDepartmentRelevantType::STAFF_POSITION_NO_RELEVANT],
                ],
                CteConstants::REF_POSITION_RELEVANT_TYPE => [
                    'unique_number' => 'department_relevant_type',
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsDepartmentRelevantType::class,
                    'where' => ['is_position' => ManualsDepartmentRelevantType::STAFF_POSITION_YES_RELEVANT],
                ],
                CteConstants::REF_DEPARTMENT_SOCIAL_SERVICE => [
                    'unique_number' => 'department_social_type',
                    'select' => [
                        "code AS unique_code",
                        'parent.code AS parent_code',
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "short_name_uz AS short_name_latin",
                        "short_name_ru AS short_name_kirill",
                        "short_name_en AS short_name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsDepartmentSocialService::class,
                    'join' => ['LEFT JOIN', "manuals_department_social_service AS parent", 'on' => ["department_social_type" => "parent_id"]],
                ],
                CteConstants::REF_POSITION => [
                    'unique_number' => 'staff_position',
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "additional_name_uz AS additional_name_latin",
                        "additional_name_ru AS additional_name_kirill",
                        "additional_name_en AS additional_name_qoraqalpoq",
                        "status",
                        "ref_position_chief_unique_code" => CteConstants::REF_POSITION_CHIEF.'.unique_code',
                    ],
                    'class' => ManualsStaffPosition::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_POSITION_CHIEF, 'on' => ["unique_number" => "staff_position_chief_id"]],
                ],
                CteConstants::REF_POSITION_CATEGORY => [
                    'unique_number' => 'staff_position_category',
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_position_unique_code" => CteConstants::REF_POSITION.'.unique_code',
                    ],
                    'class' => ManualsStaffPositionCategory::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_POSITION, 'on' => ["unique_number" => "staff_position_id"]],
                ],
                CteConstants::REF_POSITION_CHIEF => [
                    'unique_number' => 'staff_position_chief',
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsStaffPositionChief::class,
                ],
                CteConstants::REF_POSITION_COEFFICIENT => [
                    'unique_number' => 'staff_position_coefficient',
                    'select' => [
                        "code AS unique_code",
                        "coefficient",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsStaffPositionCoefficient::class,
                ],
                CteConstants::REF_POSITION_TYPE => [
                    'unique_number' => 'staff_position_type',
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_department_relevant_type_unique_code" => CteConstants::REF_DEPARTMENT_RELEVANT_TYPE.'.unique_code',
                    ],
                    'class' => ManualsStaffPositionType::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_DEPARTMENT_RELEVANT_TYPE, 'on' => ["unique_number" => "department_relevant_type_id"]],
                ],
                CteConstants::REF_COLLATERAL_TYPE => [
                    'unique_number' => 'collateral_type',
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsCollateralType::class,
                ],
                CteConstants::REF_MILITARY_DEGREE => [
                    'unique_number' => 'military_degree',
                    'select' => [
                        "code AS unique_code",
                        "CASE 
                            WHEN is_special IS TRUE THEN t_special.code
                            WHEN is_military IS TRUE THEN t_military.code
                         END AS military_degree_type_code",
                        "short_name_uz AS name_latin",
                        "short_name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(short_name_en), ''), short_name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_military_degree_structure_unique_code" => CteConstants::REF_MILITARY_DEGREE_STRUCTURE.'.unique_code',
                    ],
                    'class' => ManualsMilitaryDegree::class,
                    'join' => [
                        ['LEFT JOIN', "manuals_military_degree_type AS t_special", 'condition' => ["code" => ManualsMilitaryDegreeType::SPECIAL]],
                        ['LEFT JOIN', "manuals_military_degree_type AS t_military", 'condition' => ["code" => ManualsMilitaryDegreeType::MILITARY]],
                        ['LEFT JOIN', CteConstants::REF_MILITARY_DEGREE_STRUCTURE, 'on' => ["unique_number" => "military_degree_structure_id"]]
                    ],
                ],
                CteConstants::REF_MILITARY_DEGREE_REASON => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsMilitaryDegreeReason::class,
                ],
                CteConstants::REF_MILITARY_DEGREE_STRUCTURE => [
                    'unique_number' => 'military_degree_structure',
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsMilitaryDegreeStructure::class,
                ],
                CteConstants::REF_MILITARY_DEGREE_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsMilitaryDegreeType::class,
                ],
                CteConstants::REF_MILITARY_DEGREE_ACTION_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "short_name_uz AS name_latin",
                        "short_name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(short_name_en), ''), short_name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_command_action_type_unique_code" => CteConstants::REF_COMMAND_ACTION_TYPE.'.unique_code',
                    ],
                    'class' => ManualsEmployeeMilitaryDegreeActionType::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_COMMAND_ACTION_TYPE, 'on' => ["unique_number" => "employee_command_action_type_id"]],
                    'where' => ['like', ['code' => 'degree_%']],
                ],
                CteConstants::REF_WORK_EXPERIENCE_ACTION_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "short_name_uz AS name_latin",
                        "short_name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(short_name_en), ''), short_name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_command_action_type_unique_code" => CteConstants::REF_COMMAND_ACTION_TYPE.'.unique_code',
                    ],
                    'class' => ManualsEmployeeMilitaryDegreeActionType::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_COMMAND_ACTION_TYPE, 'on' => ["unique_number" => "employee_command_action_type_id"]],
                    'where' => ['like', ['code' => 'marked_%']],
                ],
                CteConstants::REF_PEDAGOGICAL_EXPERIENCE_ACTION_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "short_name_uz AS name_latin",
                        "short_name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(short_name_en), ''), short_name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_command_action_type_unique_code" => CteConstants::REF_COMMAND_ACTION_TYPE.'.unique_code',
                    ],
                    'class' => ManualsEmployeeMilitaryDegreeActionType::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_COMMAND_ACTION_TYPE, 'on' => ["unique_number" => "employee_command_action_type_id"]],
                    'where' => ['like', ['code' => 'pedagogical_%']],
                ],
                CteConstants::REF_MILITARY_TICKET_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEmployeeMilitaryTicketType::class,
                ],
                CteConstants::REF_COMMAND_ACTION_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_command_type_unique_code" => CteConstants::REF_COMMAND_TYPE.'.unique_code',
                    ],
                    'class' => ManualsEmployeeCommandActionType::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_COMMAND_TYPE, 'on' => ["unique_number" => "employee_command_type_id"]],
                ],
                CteConstants::REF_COMMON_COMMAND_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsCommonCommandType::class,
                ],
                CteConstants::REF_COMMAND_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "short_name_uz AS name_latin",
                        "short_name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(short_name_en), ''), short_name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEmployeeCommandType::class,
                ],
                CteConstants::REF_CATEGORY_COMMAND_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_command_action_type_unique_code" => CteConstants::REF_COMMAND_ACTION_TYPE.'.unique_code',
                    ],
                    'class' => ManualsCategoryCommandType::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_COMMAND_ACTION_TYPE, 'on' => ["unique_number" => "employee_command_action_type_id"]],
                ],
                CteConstants::REF_ACADEMIC_DEGREE_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsAcademicDegree::class,
                    'where' => ['parent_id' => null],
                ],
                CteConstants::REF_ACADEMIC_DEGREE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_academic_degree_type_unique_code" => CteConstants::REF_ACADEMIC_DEGREE_TYPE.'.unique_code',
                    ],
                    'class' => ManualsAcademicDegree::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_ACADEMIC_DEGREE_TYPE, 'on' => ["unique_number" => "parent_id"]],
                    'where' => ['not', ['parent_id' => null]],
                ],
                CteConstants::REF_ACADEMIC_TITLE_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsAcademicTitleType::class,
                ],
                CteConstants::REF_ACADEMIC_TITLE => [
                    'select' => [
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_academic_title_type_unique_code" => CteConstants::REF_ACADEMIC_TITLE_TYPE.'.unique_code',
                    ],
                    'class' => ManualsAcademicTitle::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_ACADEMIC_TITLE_TYPE, 'on' => ["unique_number" => "academic_title_type_id"]],
                ],
                CteConstants::REF_AWARDS_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsAwardsType::class,
                ],
                CteConstants::REF_CITIZENSHIP => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsCitizenship::class,
                ],
                CteConstants::REF_DOCTOR_POSITION_CATEGORY_TYPE => [
                    'unique_number' => 'doctor_position_category',
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsDoctorPositionCategoryType::class,
                ],
                CteConstants::REF_EDUCATIONAL_INFORMATION_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEducationalInformationType::class,
                ],
                CteConstants::REF_EDUCATIONAL_INSTITUTION_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_educational_information_type_unique_code" => CteConstants::REF_EDUCATIONAL_INFORMATION_TYPE.'.unique_code',
                    ],
                    'class' => ManualsEducationalInstitutionType::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_EDUCATIONAL_INFORMATION_TYPE, 'on' => ["unique_number" => "educational_information_type_id"]],
                ],
                CteConstants::REF_EDUCATIONAL_READING_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEducationalReadingType::class,
                ],
                CteConstants::REF_EDUCATIONAL_COMMAND_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_command_action_type_unique_code" => CteConstants::REF_COMMAND_ACTION_TYPE.'.unique_code',
                    ],
                    'class' => ManualsEducationCommandType::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_COMMAND_ACTION_TYPE, 'on' => ["unique_number" => "employee_command_action_type_id"]],
                ],
                CteConstants::REF_EDUCATIONAL_COMMAND_COURSE => [
                    'select' => [
                        "code AS unique_code",
                        "organization_code",
//                        "(
//                            SELECT
//                                array_agg(value) AS values_array
//                            FROM jsonb_array_elements_text(education_command_type_id::jsonb) AS value
//                        ) AS ref_education_command_type_unique_number_list",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "short_name_uz AS short_name_latin",
                        "short_name_ru AS short_name_kirill",
                        "short_name_en AS short_name_qoraqalpoq",
                        "status",
                        "ref_education_institution_unique_code" => CteConstants::REF_EDUCATIONAL_INSTITUTION.'.unique_code',
                        "ref_education_qualification_unique_code" => CteConstants::REF_EDUCATIONAL_QUALIFICATION.'.unique_code',
                    ],
                    'class' => ManualsEducationCommandCourse::class,
                    'join' => [
                        ['LEFT JOIN', CteConstants::REF_EDUCATIONAL_INSTITUTION, 'on' => ["unique_number" => "education_institution_id"]],
                        ['LEFT JOIN', CteConstants::REF_EDUCATIONAL_QUALIFICATION, 'on' => ["unique_number" => "education_specialization_id"]],
                    ],
                ],
                CteConstants::REF_EDUCATIONAL_INSTITUTION_STATUS => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEducationInstitutionStatus::class,
                ],
                CteConstants::REF_EDUCATIONAL_SPECIALIZATION_DEGREE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "status",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                    ],
                    'class' => ManualsEducationSpecializationDegree::class,
                ],
                CteConstants::REF_EDUCATIONAL_INSTITUTION => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_educational_institution_type_unique_code" => CteConstants::REF_EDUCATIONAL_INSTITUTION_TYPE.'.unique_code',
                    ],
                    'class' => ManualsEducationInstitution::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_EDUCATIONAL_INSTITUTION_TYPE, 'on' => ["unique_number" => "educational_institutions_type_id"]],
                ],
                CteConstants::REF_EDUCATIONAL_QUALIFICATION => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_educational_institution_status_unique_code" => CteConstants::REF_EDUCATIONAL_INSTITUTION_STATUS.'.unique_code',
                        "ref_education_specialization_degree_unique_code" => CteConstants::REF_EDUCATIONAL_SPECIALIZATION_DEGREE.'.unique_code',
                    ],
                    'class' => ManualsEducationInstitutionSpecialization::class,
                    'join' => [
                        ['LEFT JOIN', CteConstants::REF_EDUCATIONAL_INSTITUTION_STATUS, 'on' => ["unique_number" => "education_institution_id"]],
                        ['LEFT JOIN', CteConstants::REF_EDUCATIONAL_SPECIALIZATION_DEGREE, 'on' => ["unique_number" => "education_specialization_degree_id"]]
                    ],
                ],
                CteConstants::REF_HEALTH_LEVEL_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "short_name_uz AS name_latin",
                        "short_name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(short_name_en), ''), short_name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEmployeeHealthLevelType::class,
                ],
                CteConstants::REF_EMPLOYEE_ACTION_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEmployeeActionType::class,
                ],
                CteConstants::REF_EMPLOYEE_AGE_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEmployeeAgeType::class,
                ],
                CteConstants::REF_EMPLOYEE_ARCHIVE_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEmployeeArchiveType::class,
                ],
                CteConstants::REF_EMPLOYEE_ATTESTATION => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEmployeeAttestation::class,
                ],
                CteConstants::REF_EMPLOYEE_CATEGORY => [
                    'select' => [
                        "code AS unique_code",
                        "action_type",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEmployeeCategory::class,
                    'where' => ['parent_id' => null],
                ],
                CteConstants::REF_EMPLOYEE_CATEGORY_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "action_type",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_employee_category_unique_code" => CteConstants::REF_EMPLOYEE_CATEGORY.'.unique_code',
                    ],
                    'class' => ManualsEmployeeCategory::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_EMPLOYEE_CATEGORY, 'on' => ["unique_number" => "parent_id"]],
                    'where' => ['not', ['parent_id' => null]],
                ],
                CteConstants::REF_EMPLOYEE_DISMISSAL => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEmployeeDismissal::class,
                ],
                CteConstants::REF_EMPLOYEE_DISMISSAL_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "statute_number",
                        "statute_clause",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEmployeeDismissalType::class,
                ],
                CteConstants::REF_EMPLOYEE_ENCOURAGE_ACTION_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_command_action_type_unique_code" => CteConstants::REF_COMMAND_ACTION_TYPE.'.unique_code',
                    ],
                    'class' => ManualsEmployeeEncourageActionType::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_COMMAND_ACTION_TYPE, 'on' => ["unique_number" => "employee_command_action_type_id"]],
                ],
                CteConstants::REF_EMPLOYEE_PERMIT_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEmployeePermitType::class,
                ],
                CteConstants::REF_PUNISHMENT => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEmployeePunishment::class,
                ],
                CteConstants::REF_EMPLOYEE_REASON_DELETION => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEmployeeReasonDeletion::class,
                ],
                CteConstants::REF_EMPLOYEE_STATES_REDUCTION_TYPE => [
                    'select' => [
                        'unique_code' => "code",
                        'name_latin' => "name_uz",
                        'name_kirill' => "name_ru",
                        'name_qoraqalpoq' => "COALESCE(NULLIF(TRIM(name_en), ''), name_uz)",
                        "status"
                    ],
                    'class' => ManualsEmployeeStatesReductionType::class,
                ],
                CteConstants::REF_EMPLOYEE_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "short_name_uz AS name_latin",
                        "short_name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(short_name_en), ''), short_name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEmployeeType::class,
                ],
                CteConstants::REF_EMPLOYEE_CONTRACTION => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEmploymentContractCancellation::class,
                    'where' => ['contract_type' => 'substance'],
                ],
                CteConstants::REF_EMPLOYEE_WORK_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsEmploymentContractCancellation::class,
                    'where' => ['contract_type' => 'loading'],
                ],
                CteConstants::REF_QUARTER_ATTACHMENT_TYPE => [
                    'select' => [
                        'unique_code' => "code",
                        'name_latin' => "name_uz",
                        'name_kirill' => "name_ru",
                        'name_qoraqalpoq' => new Expression("COALESCE(NULLIF(TRIM(name_en), ''), name_uz)"),
                        "status"
                    ],
                    'class' => ManualsEmploymentContractCancellation::class,
                    'where' => ['contract_type' => 'quarter'],
                ],
                CteConstants::REF_ENCOURAGE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_employee_encourage_action_type_unique_code" => CteConstants::REF_EMPLOYEE_ENCOURAGE_ACTION_TYPE.'.unique_code',
                    ],
                    'class' => ManualsEncourage::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_EMPLOYEE_ENCOURAGE_ACTION_TYPE, 'on' => ["unique_number" => "employee_encourage_action_type_id"]],
                ],
                CteConstants::REF_GARRISON_GUARDHOUSE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsGarrisonGuardhouse::class,
                ],
                CteConstants::REF_INJURIES_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsInjuriesType::class,
                ],
                CteConstants::REF_LABOR_LEAVE_ACTION => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsLaborLeaveAction::class,
                    'where' => ['like', ['code' => 'labor_%']],
                ],
                CteConstants::REF_WORK_ACTION => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsLaborLeaveAction::class,
                    'where' => ['like', ['code' => 'work_%']],
                ],
                CteConstants::REF_ACADEMIC_ACTION => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsLaborLeaveAction::class,
                    'where' => ['like', ['code' => 'academic_%']],
                ],
                CteConstants::REF_LABOR_LEAVE_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsLaborLeaveType::class,
                ],
                CteConstants::REF_MARITAL_STATUS => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsMaritalStatus::class,
                ],
                CteConstants::REF_MEDICAL_ACCOUNT => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsMedicalControlAccount::class,
                ],
                CteConstants::REF_ORGANIZATION_AWARDS => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_awards_type_unique_code" => CteConstants::REF_AWARDS_TYPE.'.unique_code',
                        "ref_employee_encourage_action_type_unique_code" => CteConstants::REF_EMPLOYEE_ENCOURAGE_ACTION_TYPE.'.unique_code',
                    ],
                    'class' => ManualsOrganizationAwards::class,
                    'join' => [
                        ['LEFT JOIN', CteConstants::REF_AWARDS_TYPE, 'on' => ["unique_number" => "awards_type_id"]],
                        ['LEFT JOIN', CteConstants::REF_EMPLOYEE_ENCOURAGE_ACTION_TYPE, 'on' => ["unique_number" => "encourage_action_type_id"]]
                    ],
                ],
                CteConstants::REF_PERCENTAGE_SURCHARGE_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsPercentageSurchargeType::class,
                ],
                CteConstants::REF_STATE_AWARDS => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_state_awards_type_unique_code" => CteConstants::REF_STATE_AWARDS_TYPE.'.unique_code',
                    ],
                    'class' => ManualsStateAwards::class,
                    'join' => [
                        ['LEFT JOIN', CteConstants::REF_STATE_AWARDS_TYPE, 'on' => ["unique_number" => "state_awards_type_id"]]
                    ],
                ],
                CteConstants::REF_STATE_AWARDS_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_awards_type_unique_code" => CteConstants::REF_AWARDS_TYPE.'.unique_code',
                    ],
                    'class' => ManualsStateAwardsType::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_AWARDS_TYPE, 'on' => ["unique_number" => "awards_type_id"]],
                ],
                CteConstants::REF_STATE => [
                    'select' => [
                        "code AS unique_code",
                        "coato_code",
                        "service_code",
                        "state_code",
                        "phone_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ManualsState::class,
                ],
                CteConstants::REF_REGION => [
                    'select' => [
                        "code AS unique_code",
                        "coato_code",
                        "service_code",
                        "is_active",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_state_unique_code" => CteConstants::REF_STATE.'.unique_code',
                    ],
                    'class' => ManualsRegion::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_STATE, 'on' => ["unique_number" => "state_id"]],
                ],
                CteConstants::REF_DISTRICT => [
                    'select' => [
                        "code AS unique_code",
                        "coato_code",
                        "service_code",
                        "is_district",
                        "is_active",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_region_unique_code" => CteConstants::REF_REGION.'.unique_code',
                    ],
                    'class' => ManualsDistrict::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_REGION, 'on' => ["unique_number" => "region_id"]],
                ],
                CteConstants::REF_QUARTER => [
                    'select' => [
                        "coato_code",
                        "service_code",
                        "is_active",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_district_unique_code" => CteConstants::REF_DISTRICT.'.unique_code',
                    ],
                    'class' => ManualsQuarter::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_DISTRICT, 'on' => ["unique_number" => "district_id"]],
                ],
                CteConstants::REF_JETON_SERIAL => [
                    'select' => [
                        "code AS unique_code",
                        "code AS serial",
                        "status"
                    ],
                    'class' => ReferenceJetonSeries::class,
                ],
                CteConstants::REF_PASSPORT_TYPE => [
                    'select' => [
                        "token AS unique_code",
                        "service_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ReferencePassportTypes::class,
                ],
                CteConstants::REF_PASSPORT_SERIAL => [
                    'select' => [
                        "title AS unique_code",
                        "is_type AS is_local",
                        "status"
                    ],
                    'class' => ReferencePassportSerial::class,
                ],
                CteConstants::REF_NATIONALITY => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ReferenceNationality::class,
                ],
                CteConstants::REF_GENDER => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ReferenceGenders::class,
                ],
                CteConstants::REF_LANGUAGE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ReferenceLanguages::class,
                ],
                CteConstants::REF_LANGUAGE_STATUS => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status",
                        "ref_language_unique_code" => CteConstants::REF_LANGUAGE.'.unique_code',
                    ],
                    'class' => ReferenceLanguageStatus::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_LANGUAGE, 'on' => ["unique_number" => "language_id"]],
                ],
                CteConstants::REF_FAMILY_MEMBERS => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "order_family_type AS order_number",
                        "status",
                        "ref_gender_unique_code" => CteConstants::REF_GENDER.'.unique_code',
                    ],
                    'class' => ReferenceFamilyMembers::class,
                    'join' => ['LEFT JOIN', CteConstants::REF_GENDER, 'on' => ["unique_number" => "gender_id"]],
                ],
                CteConstants::REF_PARTY_MEMBERSHIP => [
                    'select' => [
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ReferencePartyMembership::class,
                ],
                CteConstants::REF_CERTIFICATE_SERIES => [
                    'select' => [
                        "code AS unique_code",
                        "organization_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ReferenceCertificateSeries::class,
                ],
                CteConstants::REF_BLOOD_GROUP => [
                    'select' => [
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ReferenceBloodGroup::class,
                ],
                CteConstants::REF_HEALTH_TYPE => [
                    'select' => [
                        "code AS unique_code",
                        "name_uz AS name_latin",
                        "name_ru AS name_kirill",
                        "COALESCE(NULLIF(TRIM(name_en), ''), name_uz) AS name_qoraqalpoq",
                        "status"
                    ],
                    'class' => ReferenceHealthType::class,
                ],
        ];
    }
}