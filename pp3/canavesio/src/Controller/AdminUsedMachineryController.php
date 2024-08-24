<?php

namespace App\Controller;

use App\Entity\UsedMachinery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminUsedMachineryController extends AbstractController
{
    #[Route('/admin/used-machinery', name: 'admin_view_used_machinery')]
    public function viewUsedMachinery(EntityManagerInterface $entityManager): Response
    {
        $usedMachineries = $entityManager->getRepository(UsedMachinery::class)->findAll();

        return $this->render('used_machinery/AdminView.html.twig', [
            'usedMachineries' => $usedMachineries,
        ]);
    }

    #[Route('/admin/used-machinery/update/{id}', name: 'admin_update_used_machinery', methods: ['POST'])]
    public function updateUsedMachinery($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $machinery = $entityManager->getRepository(UsedMachinery::class)->find($id);
    
        if (!$machinery) {
            throw $this->createNotFoundException('No machinery found for id ' . $id);
        }
    
        // Actualizar los datos del formulario
        $machinery->setMachineryName($request->request->get('machineryName'));
        $machinery->setBrand($request->request->get('brand'));
        $machinery->setYearsOld($request->request->get('yearsOld'));
        $machinery->setMonths($request->request->get('months'));
        $machinery->setHoursOfUse($request->request->get('hoursOfUse'));
        $machinery->setLastService(new \DateTime($request->request->get('lastService')));
        $machinery->setPrice($request->request->get('price'));
    
        // Manejo de la imagen subida (solo si se ha subido una nueva imagen)
        $image = $request->files->get('image');
        if ($image) {
            // Generar un nuevo nombre de archivo único y mover la imagen
            $newFilename = uniqid() . '.' . $image->guessExtension();
            $image->move($this->getParameter('images_directory'), $newFilename);
    
            // Borrar la imagen anterior si existe
            if ($machinery->getImageFilename()) {
                $oldImage = $this->getParameter('images_directory') . '/' . $machinery->getImageFilename();
                if (file_exists($oldImage)) {
                    unlink($oldImage);  // Eliminar la imagen anterior
                }
            }
    
            $machinery->setImageFilename($newFilename); // Guardar el nuevo nombre de imagen
        }
    
        // Guardar los cambios
        $entityManager->flush();
    
        // Redirigir nuevamente a la página de visualización
        return $this->redirectToRoute('admin_view_used_machinery');
    }    

    #[Route('/used-machinery/delete/{id}', name: 'admin_delete_used_machinery')]
    public function deleteUsedMachinery(EntityManagerInterface $entityManager, UsedMachinery $usedMachinery): Response
    {
        $entityManager->remove($usedMachinery);
        $entityManager->flush();

        $this->addFlash('success', 'Maquinaria eliminada con éxito.');
        return $this->redirectToRoute('admin_view_used_machinery');
    }
}