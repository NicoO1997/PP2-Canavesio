<?php

namespace App\Controller;

use App\Entity\UsedMachinery;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/admin/used-machinery/update', name: 'admin_update_used_machinery', methods: ['POST'])]
    public function updateUsedMachinery(Request $request, EntityManagerInterface $entityManager): Response
    {
        $machineryId = $request->request->get('machineryId');
        $machineryData = $request->request->get('machineries')[$machineryId];

        $machinery = $entityManager->getRepository(UsedMachinery::class)->find($machineryId);

        if ($machinery) {
            $machinery->setMachineryName($machineryData['machineryName']);
            $machinery->setBrand($machineryData['brand']);
            $machinery->setYearsOld(intval($machineryData['yearsOld']));
            $machinery->setMonths(intval($machineryData['months'])); // Asegúrate de que esto esté presente
            $machinery->setHoursOfUse(intval($machineryData['hoursOfUse']));
            $machinery->setLastService(new \DateTime($machineryData['lastService']));
            $machinery->setPrice($machineryData['price'] ? floatval($machineryData['price']) : null);

            // Handling image upload
            $imageFile = $request->files->get('machineries')[$machineryId]['image'];
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('kernel.project_dir').'/public/images',
                    $newFilename
                );
                $machinery->setImageFilename($newFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Maquinaria actualizada con éxito.');
        } else {
            $this->addFlash('error', 'Maquinaria no encontrada.');
        }

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