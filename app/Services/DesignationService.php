<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DesignationService
{
    private const CREATE_DESIGNATION_API_URL = '/api/Users/CreateDesignation';

    private const UPDATE_DESIGNATION_API_URL = '/api/Users/UpdateDesignation';

    private const SYNC_DESIGNATIONS_API_URL = '/api/Users/NeccessaryUsersDetailsInfoForMobApp';

    private const BEO_SYSTEM_SESSION_EXPIRED_CODE = 120;

    private const BEO_SYSTEM_INVALID_SESSION_CODE = 119;

    /**
     * Create a new designation via external API.
     */
    public function createDesignation(array $data, string $sessionToken): array
    {
        try {
            $response = Http::withOptions(['query' => ['sessionToken' => $sessionToken]])
                ->post(config('beosystem.base_url').self::CREATE_DESIGNATION_API_URL, [
                    'userIdCode' => $data['userIdCode'],
                    'DesignationName' => $data['name'],
                    'CompanyID' => $data['CompanyID'],
                ])
                ->throw();

            if ($response->failed()) {
                Log::error('Failed to create designation via external API', [
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

            Log::info('Response when creating designation', [
                [$responseData]
            ]);

            if (($responseData['StatusCode'] ?? null) != 200) {
                Log::error('External API returned error when creating designation', [
                    'data' => $data,
                    'response' => $responseData,
                ]);

                return [
                    'success' => false,
                    'message' => $responseData['errorMessage'] ?? 'Failed to create designation.',
                    'code' => $responseData['status'] ?? 500,
                ];
            }

            return [
                'success' => true,
                'data' => $responseData,
                'message' => 'Designation created successfully.',
                'code' => 200,
            ];

        } catch (\Exception $e) {
            Log::error('Exception when creating designation via external API', [
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
     * Update an existing designation via external API.
     */
    public function updateDesignation(int $designationId, array $data, string $sessionToken): array
    {
        try {
            $response = Http::withOptions(['query' => ['sessionToken' => $sessionToken]])
                ->post(config('beosystem.base_url').self::UPDATE_DESIGNATION_API_URL, [
                    'userIdCode' => $data['userIdCode'],
                    'DesignationID' => $designationId,
                    'DesignationName' => $data['name'],
                    'CompanyID' => $data['CompanyID'],
                ])
                ->throw();

            if ($response->failed()) {
                Log::error('Failed to update designation via external API', [
                    'designation_id' => $designationId,
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
                Log::error('External API returned error when updating designation', [
                    'designation_id' => $designationId,
                    'data' => $data,
                    'response' => $responseData,
                ]);

                return [
                    'success' => false,
                    'message' => $responseData['errorMessage'] ?? 'Failed to update designation.',
                    'code' => $responseData['StatusCode'] ?? 500,
                ];
            }

            return [
                'success' => true,
                'data' => $responseData,
                'message' => 'Designation updated successfully.',
                'code' => 200,
            ];

        } catch (\Exception $e) {
            Log::error('Exception when updating designation via external API', [
                'designation_id' => $designationId,
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
     * Sync all designations from external API.
     */
    public function syncDesignations(int $userIdCode, string $sessionToken): array
    {
        try {
            Log::info('sessionToken', [$sessionToken]);
            $response = Http::withOptions(['query' => ['sessionToken' => $sessionToken]])
                ->post(config('beosystem.base_url').self::SYNC_DESIGNATIONS_API_URL, [
                    'userIdCode' => $userIdCode,
                ])
                ->throw();

            Log::info('response', [$response]);

            if ($response->failed()) {
                Log::error('Failed to sync designations from external API', [
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

            $designations = $responseData['AUserNeccesaryDesigList_lists'] ?? [];

            if (empty($designations)) {
                Log::warning('No designations returned from external API during sync', [
                    'userIdCode' => $userIdCode,
                ]);
            }

            return [
                'success' => true,
                'designations' => $designations,
                'message' => 'Designations fetched successfully.',
                'code' => 200,
            ];

        } catch (\Exception $e) {
            Log::error('Exception when syncing designations from external API', [
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
