<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DepartmentService
{
    private const CREATE_DEPARTMENT_API_URL = '/api/Users/CreateGroup';

    private const UPDATE_DEPARTMENT_API_URL = '/api/Users/UpdateGroup';

    private const SYNC_DEPARTMENTS_API_URL = '/api/Users/GetGroupListForMobApp';

    private const BEO_SYSTEM_SESSION_EXPIRED_CODE = 120;

    private const BEO_SYSTEM_INVALID_SESSION_CODE = 119;

    /**
     * Create a new department via external API.
     */
    public function createDepartment(array $data, string $sessionToken): array
    {
        try {
            $response = Http::withOptions(['query' => ['sessionToken' => $sessionToken]])
                ->post(config('beosystem.base_url').self::CREATE_DEPARTMENT_API_URL, [
                    'userIdCode' => $data['userIdCode'],
                    'groupName' => $data['name'],
                    'companyID' => 3,
                    'supportingStaff' => $data['supporting_staff'] ?? false,
                    'outSource' => $data['out_source'] ?? false,
                    'singleSwipe' => $data['single_swipe'] ?? false,
                    'noticePeriod' => $data['notice_period'] ?? 0,
                    'isFamilyInsurancePaid' => $data['is_family_insurance_paid_by_client'] ?? 0,
                    'emails' => $data['emails'] ?? [],
                ])
                ->throw();

            if ($response->failed()) {
                Log::error('Failed to create department via external API', [
                    'data' => $data,
                    'response' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => 'BEO system unavailable. Please try again later.',
                    'code' => 503,
                ];
            }

            $responseData = $response->json();

            if ($this->isSessionError($responseData)) {
                return $this->handleSessionError($responseData);
            }

            if (($responseData['StatusCode'] ?? null) != 200) {
                Log::error('External API returned error when creating department', [
                    'data' => $data,
                    'response' => $responseData,
                ]);

                return [
                    'success' => false,
                    'message' => $responseData['errorMessage'] ?? 'Failed to create department.',
                    'code' => $responseData['StatusCode'] ?? 500,
                ];
            }

            return [
                'success' => true,
                'data' => $responseData,
                'message' => 'Department created successfully.',
                'code' => 200,
            ];

        } catch (\Exception $e) {
            Log::error('Exception when creating department via external API', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to communicate with BEO system.',
                'code' => 503,
            ];
        }
    }

    /**
     * Update an existing department via external API.
     */
    public function updateDepartment(int $departmentId, array $data, string $sessionToken): array
    {
        try {
            $response = Http::withOptions(['query' => ['sessionToken' => $sessionToken]])
                ->post(config('beosystem.base_url').self::UPDATE_DEPARTMENT_API_URL, [
                    'userIdCode' => $data['userIdCode'],
                    'groupId' => $departmentId,
                    'groupName' => $data['name'],
                    'noticePeriod' => $data['notice_period'] ?? 0,
                    'isFamilyInsurancePaid' => $data['is_family_insurance_paid_by_client'] ?? 0,
                    'companyID' => 3,
                    'supportingStaff' => $data['supporting_staff'] ?? false,
                    'outSource' => $data['out_source'] ?? false,
                    'singleSwipe' => $data['single_swipe'] ?? false,
                    'emails' => $data['emails'] ?? [],
                ])
                ->throw();

            if ($response->failed()) {
                Log::error('Failed to update department via external API', [
                    'department_id' => $departmentId,
                    'data' => $data,
                    'response' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => 'BEO system unavailable. Please try again later.',
                    'code' => 503,
                ];
            }

            $responseData = $response->json();

            if ($this->isSessionError($responseData)) {
                return $this->handleSessionError($responseData);
            }

            if (($responseData['StatusCode'] ?? null) != 200) {
                Log::error('External API returned error when updating department', [
                    'department_id' => $departmentId,
                    'data' => $data,
                    'response' => $responseData,
                ]);

                return [
                    'success' => false,
                    'message' => $responseData['errorMessage'] ?? 'Failed to update department.',
                    'code' => $responseData['StatusCode'] ?? 500,
                ];
            }

            return [
                'success' => true,
                'data' => $responseData,
                'message' => 'Department updated successfully.',
                'code' => 200,
            ];

        } catch (\Exception $e) {
            Log::error('Exception when updating department via external API', [
                'department_id' => $departmentId,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to communicate with BEO system.',
                'code' => 503,
            ];
        }
    }

    /**
     * Sync all departments from external API.
     */
    public function syncDepartments(int $userIdCode, string $sessionToken): array
    {
        try {
            $response = Http::withOptions(['query' => ['sessionToken' => $sessionToken]])
                ->post(config('beosystem.base_url').self::SYNC_DEPARTMENTS_API_URL, [
                    'userIdCode' => $userIdCode,
                ])
                ->throw();

            if ($response->failed()) {
                Log::error('Failed to sync departments from external API', [
                    'userIdCode' => $userIdCode,
                    'response' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => 'BEO system unavailable. Please try again later.',
                    'code' => 503,
                ];
            }

            $responseData = $response->json();

            if ($this->isSessionError($responseData)) {
                return $this->handleSessionError($responseData);
            }

            $departments = $responseData['group_list'] ?? [];

            if (empty($departments)) {
                Log::warning('No departments returned from external API during sync', [
                    'userIdCode' => $userIdCode,
                ]);
            }

            return [
                'success' => true,
                'departments' => $departments,
                'message' => 'Departments fetched successfully.',
                'code' => 200,
            ];

        } catch (\Exception $e) {
            Log::error('Exception when syncing departments from external API', [
                'userIdCode' => $userIdCode,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to communicate with BEO system.',
                'code' => 503,
            ];
        }
    }

    /**
     * Check if the response contains a session error.
     */
    private function isSessionError(array $responseData): bool
    {
        $status = $responseData['status'] ?? null;

        return $status == self::BEO_SYSTEM_SESSION_EXPIRED_CODE
            || $status == self::BEO_SYSTEM_INVALID_SESSION_CODE;
    }

    /**
     * Handle session-related errors.
     */
    private function handleSessionError(array $responseData): array
    {
        $status = $responseData['status'] ?? null;

        if ($status == self::BEO_SYSTEM_SESSION_EXPIRED_CODE) {
            Log::warning('BEO System session token expired');

            return [
                'success' => false,
                'message' => 'BEO System session token expired.',
                'code' => 401,
            ];
        }

        if ($status == self::BEO_SYSTEM_INVALID_SESSION_CODE) {
            Log::warning('BEO System invalid session token');

            return [
                'success' => false,
                'message' => 'BEO System invalid session token.',
                'code' => 401,
            ];
        }

        return [
            'success' => false,
            'message' => 'Authentication error.',
            'code' => 401,
        ];
    }
}
