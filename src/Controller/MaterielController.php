<?php
namespace App\Controller;

use App\Entity\Materiel;
use App\Repository\MaterielRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class MaterielController extends AbstractController
{
    private $entityManager;
    private $materielRepository;

    public function __construct(EntityManagerInterface $entityManager, MaterielRepository $materielRepository)
    {
        $this->entityManager = $entityManager;
        $this->materielRepository = $materielRepository;
    }

    #[Route('/materiel', name: 'materiel_index')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request): Response
    {
        $action = $request->query->get('action', '');
        $id = $request->query->get('id', '');
        $materielToEdit = null;

        if ($request->isMethod('POST')) {
            if (!$this->isGranted('ROLE_ADMIN')) {
                throw $this->createAccessDeniedException('Vous n\'avez pas les permissions nécessaires pour effectuer cette action.');
            }

            $formType = $request->request->get('form_type');
            if ($formType === 'create') {
                $reference = $request->request->get('reference');
                $type = $request->request->get('type');
                $statut = $request->request->get('statut');
                $processeur = $request->request->get('processeur');
                $graphique = $request->request->get('graphique');
                $mémoire = $request->request->get('mémoire');
                $série = $request->request->get('série');
                $OS = $request->request->get('OS');
                $stockage = $request->request->get('stockage');
                $taille = $request->request->get('taille');

                $materiel = new Materiel();
                $materiel->setReference($reference);
                $materiel->setType($type);
                $materiel->setStatut($statut);
                $materiel->setProcesseur($processeur);
                $materiel->setGraphique($graphique);
                $materiel->setMémoire($mémoire);
                $materiel->setSérie($série);
                $materiel->setOS($OS);
                $materiel->setStockage($stockage);
                $materiel->setTaille($taille);

                $this->entityManager->persist($materiel);
                $this->entityManager->flush();

                return $this->redirectToRoute('materiel_index');
            } elseif ($formType === 'edit') {
                $id = $request->request->get('id');
                $materiel = $this->materielRepository->find($id);

                if ($materiel) {
                    dump($request->request->all());

                    $materiel->setReference($request->request->get('reference'));
                    $materiel->setType($request->request->get('type'));
                    $materiel->setStatut($request->request->get('statut'));
                    $materiel->setProcesseur($request->request->get('processeur'));
                    $materiel->setGraphique($request->request->get('graphique'));
                    $materiel->setMémoire($request->request->get('mémoire'));
                    $materiel->setSérie($request->request->get('série'));
                    $materiel->setOS($request->request->get('OS'));
                    $materiel->setStockage($request->request->get('stockage'));
                    $materiel->setTaille($request->request->get('taille'));

                    $this->entityManager->flush();

                    return $this->redirectToRoute('materiel_index');
                }
            } elseif ($formType === 'delete') {
                $id = $request->request->get('id');
                $materiel = $this->materielRepository->find($id);

                if ($materiel) {
                    $this->entityManager->remove($materiel);
                    $this->entityManager->flush();

                    return $this->redirectToRoute('materiel_index');
                }
            }
        }

        if ($action === 'edit' && $id) {
            $materielToEdit = $this->materielRepository->find($id);
        }

        $search = $request->query->get('search', '');
        $sort = $request->query->get('sort', '');

        $queryBuilder = $this->materielRepository->createQueryBuilder('m');

        if ($search) {
            $queryBuilder->andWhere('m.reference LIKE :search OR m.type LIKE :search OR m.processeur LIKE :search OR m.graphique LIKE :search OR m.OS LIKE :search OR m.stockage LIKE :search OR m.taille LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($sort) {
            $queryBuilder->orderBy('m.' . $sort);
        }

        $materiels = $queryBuilder->getQuery()->getResult();

        return $this->render('materiel/index.html.twig', [
            'materiels' => $materiels,
            'search' => $search,
            'sort' => $sort,
            'action' => $action,
            'id' => $id,
            'materielToEdit' => $materielToEdit
        ]);
    }
}
