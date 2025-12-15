<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SupportContentService;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;

class SupportContentController extends Controller
{
    use ApiResponse; 

    protected SupportContentService $service;

    public function __construct(SupportContentService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $policies = $this->service->getActivePoliciesForApi();

        return $this->successResponse($policies, 'Policies retrieved successfully');
    }

    public function indexFaqs(): JsonResponse
    {
        $faqs = $this->service->getActiveFaqsForApi();

        return $this->successResponse($faqs, 'FAQs retrieved successfully');
    }
}