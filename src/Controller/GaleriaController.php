<?php

namespace App\Controller;

use App\Entity\Files;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class GaleriaController extends AbstractController
{
    #[Route('/galeria', name: 'app_galeria')]
    public function index(EntityManagerInterface $em, UserInterface $user): Response
    {
        $files = $em->getRepository(Files::class)->findBy(['user'=> $user->getId()]);
        return $this->render('galeria/index.html.twig', [
            'images' => $files
        ]);
    }

    #[Route('/galeria/new', name: 'app_galeriaNew')]
    public function galeriaNew(Request $request, UserInterface $user, EntityManagerInterface $em): Response
    {
        $file = $request->files->get('file');
        if ($file) {
            $titulo = $request->request->get('tittle');
            $fileDb = new Files();
            $nameFile = pathinfo($file, PATHINFO_FILENAME).'.'.$file->guessExtension();
            $file->move(dirname(__DIR__).'\\..\\public\\uploads\\', $nameFile);

            $fileDb->setTitulo($titulo);
            $fileDb->setCreatedAt(new \DateTimeImmutable('now'));
            $fileDb->setUser($user);
            $fileDb->setImagen($nameFile);

            $em->persist($fileDb);
            $em->flush();

            return $this->redirectToRoute('app_galeria');
        } else {
            return $this->render('galeria/new.html.twig', [
                'error' => 'LA IMAGEN NO ES DE UN TIPO DE IMAGEN VALIDO'
            ]);
        }
    }


    #[Route('/galeria/options/{id}', name: 'app_galeriaOptions')]
    public function galeriaOpt(Request $request, UserInterface $user, EntityManagerInterface $em, $id): Response
    {
        $files = $em->getRepository(Files::class)->find($id);

        return $this->render('galeria/option.html.twig',[
            'file'=> $files
        ]);
    }

    #[Route('/galeria/edit/{id}', name: 'app_galeriaEdit')]
    public function galeriaEdit(Request $request, UserInterface $user, EntityManagerInterface $em, $id): Response
    {
        $file = $request->files->get('file');
        $fileObject = $em->getRepository(Files::class)->find($id);
        $imagen = $fileObject->getImagen();

        if ($file) {
            $nameFile = pathinfo($file, PATHINFO_FILENAME).'.'.$file->guessExtension();
            $file->move(dirname(__DIR__).'\\..\\public\\uploads\\', $nameFile);

            $fileObject->setImagen($nameFile);
            if ($request->request->get('tittle')) {
                $titulo = $request->request->get('tittle');
                $fileObject->setTitulo($titulo);
            }
        } else {
            if ($request->request->get('tittle')) {
                $titulo = $request->request->get('tittle');
                $fileObject->setTitulo($titulo);
            }
            $fileObject->setImagen($imagen);
        }

        $em->persist($fileObject);
        $em->flush();

        if ($request->request->get('tittle') || $file) {
            return $this->redirectToRoute('app_galeria');
        }

        return $this->render('galeria/edit.html.twig', [
            'file'=> $fileObject
        ]);
    }

    #[Route('/galeria/delete/{id}', name: 'app_galeriaDelete')]
    public function galeriaDelete(Request $request, $id, EntityManagerInterface $em)
    {
        $file = $em->getRepository(Files::class)->find($id);
        $em->remove($file);
        $em->flush();

        return $this->redirectToRoute('app_galeria');
    }
}
