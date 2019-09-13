<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Compte;
use App\Entity\Partenaire;
use App\Form\CompteUserType;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use App\Repository\CompteRepository;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class CompteController extends AbstractController
{
    /**
     * @Route("/compte", name="compte_index", methods={"POST"})
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        //  $form = $this->createForm(CompteUserType::class, $user);
        // recuperation de tous les donner
        $data = $request->request->all();
        // Test Valider du ninea
        $test = $this->getDoctrine()->getRepository(Partenaire::class)->findOneBy([
            'NINEA' => $data['ninea']
        ]);
        // si le ninea est valid
        if ($test) {
            // lid du partenaire est de type int et il doit etre un objet pour que la migration se face
            // on utilisa la methode find qui nous revoi un objet
            $rien = $this->getDoctrine()->getRepository(Partenaire::class)->find($test->getId());
            $compte = new Compte();

            $compte->setSolde(0);
            $num = rand(1000000000, 9999999999);
            $sn = "SN";
            $number = $sn . $num;
            $compte->setNumCompte($number);
            /*  $compte->setSolde(0);
            $compte->setNumCompte("5631616311"); */
            // Apres recuperation de lobjet on le met dans le setter
            $compte->setPartenaire($rien);
            $entityManager->persist($compte);
            $entityManager->flush();
            $data = [
                'status1' => 201,
                'message1' => 'Le compte a été créé'
            ];

            return new JsonResponse($data, 201);
        }
        $data = [
            'status1' => 201,
            'message1' => 'bakhoul'
        ];

        return new JsonResponse($data, 201);


        // $form->handleRequest($request);
        //  $form->submit($data);



    }

    //Lister tous les comptes

    /**
     *@Route("/listercompte", name="listCompte", methods={"GET"})
     */
    public function listercompte(CompteRepository $compteRepository, SerializerInterface $serializer)
    {
        $compte = $compteRepository->findAll();
        $data = $serializer->serialize($compte, 'json', ['groups' => ['liste-compte']]);    

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    //Lister compte d'un partenaire


    /**
     * @Route("/listercompteparte", name="listerCptparte", methods={"GET"})
     */
    public function listercompteparte(Request $request, SerializerInterface $serializer): Response
    {
        $data = $request->request->all();
        $user = $this->getUser();
        $partenaire = $user->getPartenaire();
        $users = $this->getDoctrine()->getRepository('App:Compte')->findBy(['partenaire' => $partenaire]);
        $data = $serializer->serialize($users, 'json', ['groups' => ['lister-cptparte']]);
        return new Response(
            $data,
            200,
            [
                'Content-Type' => 'application/json'
            ]
        );
    }


    //Alouer compte à un user par partenaire

    /**
     * @Route("/addCompte", name="ajou_compte", methods={"POST","PUT"})
     *@IsGranted({"ROLE_ADMIN"})
     */
    public function addcompteuser(Request $request,  CompteRepository $compte, UserRepository $users, EntityManagerInterface $entityManager)
    {

        $values = $request->request->all();

        $ut = $users->findOneBy(['username' => $values['username']]);
        $c = $compte->findById($values['compte']);


        if (!$ut) {
            return new Response("Ce username n'existe pas ", Response::HTTP_CREATED);
        }


        $ut->setCompte($c[0]);

        $entityManager->flush();
        $data = [
            'status14' => 200,
            'message14' => 'Le compte a bien été  bien ajoute'
        ];
        return new JsonResponse($data);
    }

    //Rechercher Compte

    /**
     *@Route("/cherchercompte",name="cherchercompte", methods ={"GET","POST"})
     */

    public function findcompte(Request $request,  SerializerInterface $serializer)
    {
        $values = json_decode($request->getContent());
        $compte = new Compte();
        $compte->setNumCompte($values->NumCompte);

        $repository = $this->getDoctrine()->getRepository(Compte::class);
        $compte = $repository->findByNumCompte($values->NumCompte);

        $data = $serializer->serialize($compte, 'json', ['groups' => ['comptes']]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }




    /**
     * @Route("/new", name="compte_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $compte = new Compte();
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($compte);
            $entityManager->flush();

            return $this->redirectToRoute('compte_index');
        }

        return $this->render('compte/new.html.twig', [
            'compte' => $compte,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="compte_show", methods={"GET"})
     */
    public function show(Compte $compte): Response
    {
        return $this->render('compte/show.html.twig', [
            'compte' => $compte,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="compte_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Compte $compte): Response
    {
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('compte_index');
        }

        return $this->render('compte/edit.html.twig', [
            'compte' => $compte,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="compte_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Compte $compte): Response
    {
        if ($this->isCsrfTokenValid('delete' . $compte->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($compte);
            $entityManager->flush();
        }

        return $this->redirectToRoute('compte_index');
    }


    /**
     * @Route("/listerpartenaire/{id}", name="list_parte", methods={"GET"})
     */
    public function listerpartenaire(PartenaireRepository $partenaireRepository, SerializerInterface $serializer)
    {
        $partenaire = $partenaireRepository->findAll();
        $data = $serializer->serialize($partenaire, 'json', ['groups' => ['lister-partenaire']]);

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

}
