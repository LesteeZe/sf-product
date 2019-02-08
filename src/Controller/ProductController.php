<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use Symfony\Component\Validator\Constraints\IsTrue;

class ProductController extends Controller
{
    /**
     * @Route("/product", name="product_index")
     */
    public function index()
    {
        $repository = $this
            ->getDoctrine()
            ->getRepository(Product::class);

        $products = $repository->findAll();

        return $this->render('Product/index.html.twig', [
            'products' => $products
        ]);
    }




    /**
     * @Route(
     *     "/product/{id}",
     *     name="product_show",
     *     requirements={"id": "\d+"}
     * )
     */
    public function show($id)
    {
        $product = $this->findProductById($id);

        return $this->render('Product/show.html.twig', [
            'product' => $product,
        ]);
    }




    /**
     * @Route("/product/create", name="product_create")
     */
    public function create(Request $request)
    {
        $product = new Product();

        $form = $this->createProductForm($product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'id' => $product->getId()
            ]);
        }

        return $this->render('Product/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }





    /**
     * @Route("/product/{id}/update", name="product_update")
     */
    public function update(Request $request)
    {
        $id = $request->attributes->get('id');

        $product = $this->findProductById($id);

        $form = $this->createProductForm($product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'id' => $product->getId()
            ]);
        }

        return $this->render('Product/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }





    /**
     * @Route("/product/{id}/delete", name="product_delete")
     */
    public function delete(Request $request)
    {
        $id = $request->attributes->get('id');

        $product = $this->findProductById($id);

        $form = $this
            ->createFormBuilder()
            ->add('confirm', Type\CheckboxType::class, [
                'required'    => false,
                'constraints' => [
                    new IsTrue(),
                ]
            ])
            ->add('submit', Type\SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($product);
            $em->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('Product/delete.html.twig', [
            'form' => $form->createView(),
        ]);
    }




    private function findProductById($id)
    {
        $repository = $this
            ->getDoctrine()
            ->getRepository(Product::class);

        $product = $repository->find($id);

        if (null === $product) {
            throw $this->createNotFoundException("Produit introuvable");
        }

        return $product;
    }




    private function createProductForm(Product $product)
    {
        return $this
            ->createFormBuilder($product)
            ->add('designation', Type\TextType::class)
            ->add('reference', Type\TextType::class)
            ->add('brand', Type\TextType::class)
            ->add('price', Type\MoneyType::class)
            ->add('stock', Type\IntegerType::class)
            ->add('active', Type\CheckboxType::class)
            ->add('description', Type\TextareaType::class)
            ->add('submit', Type\SubmitType::class)
            ->getForm();
    }
}
