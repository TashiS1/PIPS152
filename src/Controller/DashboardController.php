<?php
namespace App\Controller;

use App\Entity\Affectation;
use App\Entity\Materiel;
use App\Entity\Permanent;
use App\Repository\AffectationRepository;
use App\Repository\MaterielRepository;
use App\Repository\PermanentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(
        Request $request,
        MaterielRepository $materielRepository,
        PermanentRepository $permanentRepository,
        AffectationRepository $affectationRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $materiels = $materielRepository->findAll();
        $permanents = $permanentRepository->findAll();

        $searchTerm = $request->query->get('search', '');

        if ($searchTerm) {
            $queryBuilder = $affectationRepository->createQueryBuilder('a')
                ->leftJoin('a.materiel', 'm')
                ->leftJoin('a.permanent', 'p')
                ->where('m.reference LIKE :searchTerm OR p.name LIKE :searchTerm OR a.status LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');

            $affectations = $queryBuilder->getQuery()->getResult();
        } else {
            $affectations = $affectationRepository->findAll();
        }

        $action = $request->query->get('action', '');
        $id = $request->query->get('id', '');
        $type = $request->query->get('type', '');

        $permanentToEdit = null;
        $affectationToEdit = null;

        if ($request->isMethod('POST')) {
            $formType = $request->request->get('form_type');
            if ($formType === 'create_permanent') {
                $name = $request->request->get('name');
                $email = $request->request->get('email');
                $permanent = new Permanent();
                $permanent->setName($name);
                $permanent->setEmail($email);
                $entityManager->persist($permanent);
                $entityManager->flush();

                return $this->redirectToRoute('dashboard');
            } elseif ($formType === 'edit_permanent') {
                $id = $request->request->get('id');
                $permanent = $permanentRepository->find($id);
                if ($permanent) {
                    $permanent->setName($request->request->get('name'));
                    $permanent->setEmail($request->request->get('email'));
                    $entityManager->flush();

                    return $this->redirectToRoute('dashboard');
                }
            } elseif ($formType === 'delete_permanent') {
                $id = $request->request->get('id');
                $permanent = $permanentRepository->find($id);
                if ($permanent) {
                    $entityManager->remove($permanent);
                    $entityManager->flush();

                    return $this->redirectToRoute('dashboard');
                }
            } elseif ($formType === 'create_affectation') {
                $materielRef = $request->request->get('materiel');
                $permanentId = $request->request->get('permanent');
                $status = $request->request->get('status');

                $materiel = $materielRepository->findOneBy(['reference' => $materielRef]);
                $permanent = $permanentRepository->find($permanentId);

                if ($materiel && $permanent) {
                    $affectation = new Affectation();
                    $affectation->setMateriel($materiel);
                    $affectation->setPermanent($permanent);
                    $affectation->setDateAffectation(new \DateTime());
                    $affectation->setStatus($status);

                    $materiel->setStatut('En utilisation');
                    $entityManager->persist($affectation);
                    $entityManager->persist($materiel);
                    $entityManager->flush();

                    return $this->redirectToRoute('dashboard');
                }
            } elseif ($formType === 'edit_affectation') {
                $id = $request->request->get('id');
                $affectation = $affectationRepository->find($id);
                if ($affectation) {
                    $materielRef = $request->request->get('materiel');
                    $permanentId = $request->request->get('permanent');
                    $date = $request->request->get('date');
                    $status = $request->request->get('status');

                    $materiel = $materielRepository->findOneBy(['reference' => $materielRef]);
                    $permanent = $permanentRepository->find($permanentId);

                    if ($materiel && $permanent) {
                        $affectation->setMateriel($materiel);
                        $affectation->setPermanent($permanent);
                        $affectation->setDateAffectation(new \DateTime($date));
                        $affectation->setStatus($status);
                        $entityManager->flush();

                        return $this->redirectToRoute('dashboard');
                    }
                }
            } elseif ($formType === 'delete_affectation') {
                $id = $request->request->get('id');
                $affectation = $affectationRepository->find($id);
                if ($affectation) {
                    $entityManager->remove($affectation);
                    $entityManager->flush();

                    return $this->redirectToRoute('dashboard');
                }
            }
        }

        if ($action === 'edit' && $id) {
            if ($type === 'permanent') {
                $permanentToEdit = $permanentRepository->find($id);
            } elseif ($type === 'affectation') {
                $affectationToEdit = $affectationRepository->find($id);
            }
        }

        return $this->render('dashboard/index.html.twig', [
            'materiels' => $materiels,
            'permanents' => $permanents,
            'affectations' => $affectations,
            'action' => $action,
            'id' => $id,
            'type' => $type,
            'permanentToEdit' => $permanentToEdit,
            'affectationToEdit' => $affectationToEdit,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminIndex(
        MaterielRepository $materielRepository,
        PermanentRepository $permanentRepository,
        AffectationRepository $affectationRepository
    ): Response {
        $materiels = $materielRepository->findAll();
        $permanents = $permanentRepository->findAll();
        $affectations = $affectationRepository->findAll();

        return $this->render('dashboard/admin.html.twig', [
            'materiels' => $materiels,
            'permanents' => $permanents,
            'affectations' => $affectations,
        ]);
    }

    #[Route('/check-materiel-status', name: 'check_materiel_status')]
    public function checkMaterielStatus(Request $request, MaterielRepository $materielRepository, AffectationRepository $affectationRepository): JsonResponse
    {
        $reference = $request->query->get('reference');
        $materiel = $materielRepository->findOneBy(['reference' => $reference]);

        if ($materiel) {
            $affectation = $affectationRepository->findOneBy(['materiel' => $materiel, 'status' => 'affecté']);

            if ($affectation) {
                return new JsonResponse([
                    'status' => 'En utilisation',
                    'permanentId' => $affectation->getPermanent()->getId(),
                    'permanentName' => $affectation->getPermanent()->getName()
                ]);
            }
        }

        return new JsonResponse(['status' => 'Disponible']);
    }
}
