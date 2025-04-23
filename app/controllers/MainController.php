<?php
namespace app\controllers;

class MainController extends Controller
{
    public function homepage()
    {
        $this->view('main/homepage');
    }

    public function about()
    {
        $this->view('main/about');
    }

    public function contact()
    {
        $this->view('main/contact');
    }

    public function japan()
    {
        $this->view('travel/japan');
    }

    public function turkey()
    {
        $this->view('travel/turkey');
    }

    public function montenegro()
    {
        $this->view('travel/montenegro');
    }
}
