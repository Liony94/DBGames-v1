<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversation>
 *
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    public function findConversationsByUser(User $user)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->innerJoin('c.participants', 'p')
            ->leftJoin('c.messages', 'm')
            ->addSelect('m')
            ->where('p.id = :userId')
            ->setParameter('userId', $user->getId())
            ->orderBy('m.sentAt', 'DESC');

        $conversations = $qb->getQuery()->getResult();

        foreach ($conversations as $conversation) {
            $unreadMessages = $this->getEntityManager()->getRepository(Message::class)->findBy(['conversation' => $conversation, 'isRead' => false]);
            $conversation->setUnreadCount(count($unreadMessages));
        }

        return $conversations;
    }
}
