<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class ReservationController extends AbstractController
{
    #[Route('/prenota', name: 'reservation_send', methods: ['POST'])]
    public function send(
        Request $request,
        MailerInterface $mailer,
        #[Autowire('%reservation_recipient%')] string $recipient,
    ): JsonResponse {
        $nome = trim((string) $request->request->get('nome'));
        $telefono = trim((string) $request->request->get('telefono'));
        $coperti = trim((string) $request->request->get('coperti'));
        $data = trim((string) $request->request->get('data'));
        $ora = trim((string) $request->request->get('ora'));
        $note = trim((string) $request->request->get('note'));

        // Honeypot anti-spam: se compilato, fingiamo il successo e usciamo
        if ('' !== trim((string) $request->request->get('website'))) {
            return new JsonResponse(['ok' => true]);
        }

        if ('' === $nome || '' === $telefono || '' === $coperti || '' === $data || '' === $ora) {
            return new JsonResponse(
                ['ok' => false, 'message' => 'Compila tutti i campi obbligatori.'],
                422,
            );
        }

        $email = (new Email())
            ->from($recipient)
            ->to($recipient)
            ->subject(sprintf('Nuova prenotazione — %s (%s coperti)', $nome, $coperti))
            ->text($this->buildBody($nome, $telefono, $coperti, $data, $ora, $note));

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface) {
            return new JsonResponse(
                ['ok' => false, 'message' => 'Invio non riuscito. Riprova o chiamaci direttamente.'],
                502,
            );
        }

        return new JsonResponse(['ok' => true]);
    }

    private function buildBody(
        string $nome,
        string $telefono,
        string $coperti,
        string $data,
        string $ora,
        string $note,
    ): string {
        $lines = [
            'Nuova richiesta di prenotazione',
            '',
            'Nome e cognome: '.$nome,
            'Telefono: '.$telefono,
            'Coperti: '.$coperti,
            'Data: '.$data,
            'Ora: '.$ora,
        ];

        if ('' !== $note) {
            $lines[] = 'Note: '.$note;
        }

        return implode("\n", $lines);
    }
}
