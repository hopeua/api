<?php

namespace Hope\RestBundle\Entity;

use Doctrine\ORM\EntityRepository;

class EpisodeRepository extends EntityRepository
{
    public function getByParams($params)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query
            ->select('e')
            ->from('HopeRestBundle:Episode', 'e');


        //код видео
        if(!empty($params['code'])){
            $query->andWhere('e.code = :code');
            $query->setParameter('code', $params['code']);
        }

        //код программы
        if(!empty($params['program_code'])){
                $query->leftjoin('HopeRestBundle:Program', 'p', 'WITH', 'e.program_id = p.id');
                $query->andWhere('p.code = :pcode');
                $query->setParameter('pcode', $params['program_code']);

        }

        //код категории
        if(!empty($params['program_category'])) {
            if(empty($params['program_code'])){
                $query->leftjoin('HopeRestBundle:Program', 'p', 'WITH', 'e.program_id = p.id');
            }
            $query->andWhere('p.category_id = :category');
            $query->setParameter('category',$params['program_category']);
        }

        if(!empty($params['text'])) {
            $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('e.title', ':text'),
                    $query->expr()->like('e.description', ':text'),
                    $query->expr()->like('e.author', ':text')
                )
            );
            $query->setParameter('text','%'.$params['text'].'%');

        }

        $limit              = explode('|',$params['limit']);
        $offset             = $limit[0]; // начинаем считывать с N записи
        $quantity           = $limit[1]; // количество выбираемых записей

        $query->orderBy('e.publish_time', 'DESC');
        $query->setMaxResults($quantity);
        $query->setFirstResult($offset);

        /*$qqq    = $query->getQuery();
        print '<pre>';
                print_r(array(
                    'sql'        => $qqq->getSQL(),
                    'dql'        => $qqq->getDql(),
                    'parameters' => $qqq->getParameters(),
                ));
                print '</pre>';
                die();
        */
        $results = $query->getQuery()->getResult();
        return $results;
    }
} 