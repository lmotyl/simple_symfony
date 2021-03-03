<?php

namespace App\Controller;

use App\Repository\UserRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use Phalcon\Assets\Inline\Js;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Rest\Route("/user", name="index_user", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        $users = $this->userRepository->findBy([], ['points' => 'DESC']);;
        $data = [];

        foreach($users as $user) {
            $data[] = $user->toArray();
        }


        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Rest\Route("/user", name="add_user", methods={"POST"})
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $id = $data['id'] ?? null;
        $username = $data['username'] ?? null;
        $points = $data['points'] ?? null;

        $this->userRepository->persistUser($username, $points);

        return new JsonResponse(['status' => 'OK'], Response::HTTP_OK);
    }

    /**
     * @Rest\Route("/user/username", name="delete_user_username", methods={"DELETE"})
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteByUsername(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'] ?? null;

        $this->userRepository->removeByUsername($username);

        return new JsonResponse(Response::HTTP_OK);
    }

    /**
     * @Rest\Route("/user/{id}", name="delete_user", methods={"DELETE"})
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function deleteById($id): JsonResponse
    {

        $this->userRepository->removeById($id);

        return new JsonResponse(Response::HTTP_OK);
    }

}
