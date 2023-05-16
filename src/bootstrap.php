<?php

declare(strict_types=1);

namespace SierraKomodo\BudgetTracking;

use SierraKomodo\BudgetTracking\Factory\EntityManagerFactory;

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../src/environment.php');

EntityManagerFactory::getEntityManager();
