<?php

namespace App\Controller;

use App\Service\FileUploader;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestionnaire')]
class UploadController extends AbstractController
{
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
    #[Route('/fusion', name: 'fusion')]
    public function fusion(Request $request, $uploadDir, FileUploader $uploader, LoggerInterface $logger): Response
    {
        $handle1 = fopen("../var/uploads/small-french-data.csv", "r");
        $handle2 = fopen("../var/uploads/small-german-data.csv", "r");
        $fusion ="../public/uploads/test1.csv";
        $fp = fopen($fusion, 'wb');
        $liste = array();
        if ($handle1){
            $ligne1 = fgetcsv($handle1, 12, ",");
            if ($handle2){
                $ligne2= fgetcsv($handle2, 12, ",");
                while($ligne1){
                    $liste[] = $ligne1;
                    $ligne1 = fgetcsv($handle1, 12, ",");
                }

                while($ligne2){
                    $liste[] = $ligne2;
                    $ligne2 = fgetcsv($handle2, 12, ",");
                }
                fclose($handle1);
                fclose($handle2);
            }
            else{
                echo "Ouverture fichier 2 impossible";
            }
        }
        else{
            echo "Ouverture fichier 1 impossible";
        }

        foreach ($liste as $fields) {
            fputcsv($fp, $fields);

        }
        fclose($fp);
        dump($liste);
        exit();
    }
}
