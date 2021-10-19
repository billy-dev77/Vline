<?php

namespace App\Controller;

use App\Service\FileUploader;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestionnaire')]
class UploadController extends AbstractController
{

    public function role(Request $request): Response
    {
        $roles=$this->getUser()->getRoles();
        $nom=$this->getUser()->getUserIdentifier();
        if ($roles[0] == 'gestionnaire'){
            return $this->render('homepage/index.html.twig');
        }
        else{
            $this->render('utilisateur/index.html.twig');
        }
    }

    #[Route('/file', name: 'file')]
    public function index(): Response
    {
        return $this->render('homepage/index.html.twig');
    }

    #[Route('/upload', name: 'upload')]
    public function upload(Request $request, string $uploadDir,
                          FileUploader $uploader, LoggerInterface $logger): Response
    {
        $token = $request->get("token");

        if (!$this->isCsrfTokenValid('upload', $token))
        {
            $logger->info("CSRF failure");

            return new Response("Operation not allowed",  Response::HTTP_BAD_REQUEST,
                ['content-type' => 'text/plain']);
        }

        $file1 = $request->files->get('myfile1');
        $file2 = $request->files->get('myfile2');

        if (empty($file1)&& empty($file2))
        {
            return new Response("No file specified",
                Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);
        }

        $filename1 = $file1->getClientOriginalName();
        $filename2 = $file2->getClientOriginalName();
        $uploader->upload1($uploadDir, $file1, $filename1);
        $uploader->upload2($uploadDir, $file2, $filename2);

        return new Response("File uploaded",  Response::HTTP_OK,
            ['content-type' => 'text/plain']);
    }
}
