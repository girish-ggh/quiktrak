<?php

namespace App\Controller;

use App\Entity\Job;
use App\Repository\InspectorRepository;
use App\Repository\JobRepository;
use App\Service\TimeZoneService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;

class JobController extends AbstractController
{
    private TimeZoneService $timeZoneService;

    public function __construct(TimeZoneService $timeZoneService)
    {
        $this->timeZoneService = $timeZoneService;
    }

    #[Route('/api/jobs/schedule', name: 'schedule_job', methods: ['POST'])]

    /**
     * Schedule a new job.
     *
     * @Route("/api/jobs/schedule", name="schedule_job", methods={"POST"})
     * @OA\Post(
     *     path="/api/jobs/schedule",
     *     summary="Schedule a new job",
     *     description="Creates and schedules a new job for an inspector",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "scheduledDate", "inspector_id"},
     *             @OA\Property(property="title", type="string", description="The title of the job"),
     *             @OA\Property(property="description", type="string", description="The description of the job"),
     *             @OA\Property(property="scheduledDate", type="string", format="date-time", description="The date and time when the job is scheduled (in UTC)"),
     *             @OA\Property(property="inspector_id", type="integer", description="The ID of the inspector")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Job scheduled successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Job scheduled successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Missing required fields",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Missing required fields")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Inspector not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Inspector not found")
     *         )
     *     )
     * )
     */
    public function scheduleJob(Request $request, JobRepository $jobRepository, InspectorRepository $inspectorRepository): JsonResponse
    {
        // Decode the JSON request body
        $data = json_decode($request->getContent(), true);

        // Validate incoming data
        if (empty($data['title']) || empty($data['scheduledDate']) || empty($data['inspector_id'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        // Retrieve inspector by ID
        $inspector = $inspectorRepository->find($data['inspector_id']);
        if (!$inspector) {
            return new JsonResponse(['error' => 'Inspector not found'], 404);
        }

        // Create new job entity
        $job = new Job();
        $job->setTitle($data['title'])
            ->setDescription($data['description'] ?? null)
            ->setScheduledDate(new \DateTime($data['scheduledDate'])) // Ensure it's stored in UTC
            ->setInspector($inspector)
            ->setStatus('pending');

        // Save job using JobRepository
        $jobRepository->save($job); // Use the custom save method

        return new JsonResponse(['message' => 'Job scheduled successfully'], 201);
    }

    #[Route('/api/jobs/{id}', name: 'get_job', methods: ['GET'])]

    /**
     * Get a job by ID.
     *
     * @Route("/api/jobs/{id}", name="get_job", methods={"GET"})
     * @OA\Get(
     *     path="/api/jobs/{id}",
     *     summary="Get a job by ID",
     *     description="Retrieve the details of a specific job",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="The ID of the job"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Job details",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="scheduledDate", type="string", format="date-time"),
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(
     *                 property="inspector",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="location", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Job not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Job not found")
     *         )
     *     )
     * )
     */
    public function getJob(int $id, JobRepository $jobRepository): JsonResponse
    {
        // Retrieve job by ID
        $job = $jobRepository->find($id);
        if (!$job) {
            return new JsonResponse(['error' => 'Job not found'], 404);
        }

        return new JsonResponse([
            'id' => $job->getId(),
            'title' => $job->getTitle(),
            'description' => $job->getDescription(),
            'scheduledDate' => $job->getScheduledDate()->format('Y-m-d H:i:s'),
            'status' => $job->getStatus(),
            'inspector' => [
                'id' => $job->getInspector()->getId(),
                'name' => $job->getInspector()->getName(),
                'location' => $job->getInspector()->getLocation()
            ]
        ]);
    }

    #[Route('/api/jobs/{id}/complete', name: 'complete_job', methods: ['POST'])]
    
    /**
     * Mark a job as completed.
     *
     * @Route("/api/jobs/{id}/complete", name="complete_job", methods={"POST"})
     * @OA\Post(
     *     path="/api/jobs/{id}/complete",
     *     summary="Mark a job as completed",
     *     description="Complete a job and add an assessment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="The ID of the job"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"assessment"},
     *             @OA\Property(property="assessment", type="string", description="The assessment of the job")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Job completed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Job completed successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Assessment is required",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Assessment is required")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Job not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Job not found")
     *         )
     *     )
     * )
     */
    public function completeJob(Request $request, int $id, JobRepository $jobRepository): JsonResponse
    {
        // Retrieve job by ID
        $job = $jobRepository->find($id);
        if (!$job) {
            return new JsonResponse(['error' => 'Job not found'], 404);
        }

        // Decode the JSON request body
        $data = json_decode($request->getContent(), true);
        
        // Validate incoming data
        if (empty($data['assessment'])) {
            return new JsonResponse(['error' => 'Assessment is required'], 400);
        }

        // Update job status and assessment
        $job->setStatus('completed')
            ->setAssessment($data['assessment']); // Make sure you have an assessment field in the Job entity

        // Save changes
        $jobRepository->save($job);

        return new JsonResponse(['message' => 'Job completed successfully'], 200);
    }
}
