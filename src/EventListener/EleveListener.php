<<<<<<< HEAD
<?php

namespace App\EventListener;

class EleveListener
{
}
=======
<?php

namespace App\EventListener;

use App\Entity\Eleve;
use Doctrine\ORM\Event\LifecycleEventArgs;

class EleveListener
{
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // Vérifiez si l'entité est un Eleve
        if ($entity instanceof Eleve) {
            $parent = $entity->getParents();

            // Vérifiez si le parent n'a pas d'autres élèves
            if (count($parent->getEleves()) === 1) {
                // Supprimez le parent
                $entityManager = $args->getEntityManager();
                $entityManager->remove($parent);
            }
        }
    }
}
>>>>>>> ab957363ad7b49516ce7ae8d38ce1e848b12dc33
