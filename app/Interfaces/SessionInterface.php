<?php


namespace App\Interfaces;


interface SessionInterface
{
    public function getAllSessions();

    public function getCurrentSession();

    public function createSession($request);

    public function updateSession($request);

    public function deleteSession($id);
}
