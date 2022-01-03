
throw new \Exception('Message');

php ../composer.phar update

/***********************

http://example.com/somewhere?_switch_user=thomas
http://example.com/somewhere?_switch_user=_exit

RewriteEngine On
#RewriteCond %{REQUEST_FILENAME} -f
#    RewriteRule ^ - [L]

    # Rewrite all other queries to the front controller.
#    RewriteRule ^ %{ENV:BASE}/app.php [L]

RewriteRule ^ web/app.php
#deny from all
Options All -Indexes
#Options -Indexes

/**********************/
        $user = $this->getUser();
        $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Bodoni MT');
        
        $objPHPExcel->getProperties()->setCreator($user->getNom())
            ->setLastModifiedBy("SNC RTIE")
            ->setTitle("SNC RTIE Journal des missions")
            ->setSubject("SNC RTIE Journal des mission")
            ->setDescription("SNC RTIE")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("SNC RTIE Journal des missions");
        $dateTimeNow = time();

        $feuil = $objPHPExcel->getActiveSheet();

        $feuil->setCellValue('B1', 'Mission RTIE');

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($objPHPExcel, 'Excel2007');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $nom = "Journal_Des_Missions_SNC_RTIE_" . date('Y-m-d__H-i-s') . ".xlsx";
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,$nom
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;


/**********************/
$query = $entityManager->createQuery(
    'SELECT p
    FROM AppBundle:Product p
    WHERE p.price > :price
    ORDER BY p.price ASC'
)->setParameter('price', 19.99);

$products = $query->getResult();
/*********************/
SELECT
    m.id,m.code,m.avance,m.code,m_dm,m_fm,(m_dm + m_fm) as total_depense,(m.avance - m_dm - m_fm) as solde
FROM mission m
LEFT JOIN (
    SELECT mission_id, SUM(montant) AS m_dm
    FROM depense_mission
    GROUP BY mission_id
    ) FM
    ON m.id = FM.mission_id
LEFT JOIN (
    SELECT mission_id, SUM(montant) AS m_fm
    FROM frais_mission
    GROUP BY mission_id
    ) MD
    ON m.id = MD.mission_id

/************************/

SELECT
    m.id,m.code,m.avance,m.code,m_dm,m_fm,(m_dm + m_fm) as total_depense,(m.avance - m_dm - m_fm) as solde
FROM mission m
LEFT JOIN (
    SELECT mission_id, SUM(montant) AS m_dm
    FROM depense_mission
    GROUP BY mission_id
    ) FM
    ON m.id = FM.mission_id
LEFT JOIN (
    SELECT mission_id, SUM(montant) AS m_fm
    FROM frais_mission
    GROUP BY mission_id
    ) MD
    ON m.id = MD.mission_id
WHERE m.code LIKE '2019M00%'
/***********************/
$em = $this->getEntityManager();
$query = $em->createQuery("SELECT
    m.id,m.code,m.avance,m.code,m_dm,m_fm,(m_dm + m_fm) as total_depense,(m.avance - m_dm - m_fm) as solde
FROM AppBundle:Mission m
LEFT JOIN (
    SELECT mission, SUM(montant) AS m_dm
    FROM AppBundle:Depense_mission
    GROUP BY mission
    ) FM
    ON m.id = FM.mission
LEFT JOIN (
    SELECT mission, SUM(montant) AS m_fm
    FROM AppBundle:Frais_mission
    GROUP BY mission
    ) MD
    ON m.id = MD.mission
WHERE m.code LIKE ':id%'");
$query->setParameter(1, $id);
$res = $query->getResult();
return $res;

/***********************/
$em = $this->getEntityManager();
        $query = $em->createQuery("SELECT r.id,l.id as idlicenciement,r.recruter,l.licencier,l.motif FROM AppBundle:Recrutement r LEFT JOIN AppBundle:Licenciement l  WITH r.id = l.recrutement WHERE r.user = ?1");
        $query->setParameter(1, $id);
        $res = $query->getResult();
        return $res;
/***********************/
public function getDescriptionArticle($id)
  {
      
     $subquery = $this->_em->createQueryBuilder();
     $subquery ->select('DISTINCT(da.description)');
     $subquery ->from('AppBundle:DescriptionArticle', 'da');
     $subquery ->where('da.article=:id');

     $qb = $this ->createQueryBuilder('d');
     $qb->where($qb->expr()->notIn('d.id', $subquery->getDQL()));
     $qb ->setParameter('id',$id);
     return $qb;
    //return $qb->getQuery()->getResult();

    




  }

/**********************/
SELECT v2_document.title, GROUP_CONCAT(DISTINCT CONCAT(v2_domain.id, ' ',v2_domain_lang.name)) AS domain_name, GROUP_CONCAT(DISTINCT CAST(v2_file.name AS CHAR)) AS file_name
FROM v2_document
LEFT JOIN v2_domain_document ON v2_document.id = v2_domain_document.id_document
LEFT JOIN v2_domain ON v2_domain_document.id_domain = v2_domain.id
LEFT JOIN v2_domain_lang ON v2_domain.id = v2_domain_lang.reference
LEFT JOIN v2_language ON v2_domain_lang.LANGUAGE = v2_language.id
LEFT JOIN v2_file_document ON v2_document.id = v2_file_document.id_document
LEFT JOIN v2_file ON v2_file_document.id_file = v2_file.id
WHERE v2_language.id = 1


<script>

        $(document).ready(function() {
        // On récupère la balise <div> en question qui contient l'attribut « data-prototype » qui nous intéresse
        var $container = $('#employe_recrutements');
        // On définit une fonction qui va ajouter un champ
        function add_category() {
            // On définit le numéro du champ (en comptant le nombre de champs déjà ajoutés)
            index = $container.children().length;
            // On ajoute à la fin de la balise <div> le contenu de l'attribut « data-prototype »
            // Après avoir remplacé la variable __name__ qu'il contient par le numéro du champ
            //$container.append($($container.attr('data-prototype').replace(/__name__label__/g, "")));
            $container.append($($container.attr('data-prototype').replace(/__name__/g, index)));

            // On ajoute également un bouton pour pouvoir supprimer la catégorie
            $container.append($('<a href="#" id="delete_category_' + index + '" class="btn btn-danger">Supprimer</a><br /><br />'));
            // On supprime le champ à chaque clic sur le lien de suppression
            $('#delete_category_' + index).click(function() {
                $(this).prev().remove();
                // $(this).prev() est le template ajouté
                $(this).remove();
                // $(this) est le lien de suppression 
                return false;
            });
        }
    
        // On ajoute un premier champ directement s'il n'en existe pas déjà un (cas d'un nouvel article par exemple)
        console.log($container.children().length );
        if($container.children().length == 0) {
            add_category();
        }
        // On ajoute un nouveau champ à chaque clic sur le lien d'ajout
        $('#add_category').click(function() {
            add_category();
            return false;
        }
        );
    }
    );

</script>

 @JoinColumn(name="user_id", referencedColumnName="id", nullable = false)



**************************
 function onClickAjouter(event) {
        alert("2");
        event.preventDefault();

    }
    document.querySelectorAll('a.js-ajouter').forEach(function (link) {
        alert("1");
        alert(link);
        link.addEventListener('click', onClickAjouter);
    }) 
*****************************

    ligne += "<a href='' onclick='fc(\"" + data.sites[i].code + "\",\"" + data.sites[i].nom + "\")'>" + data.sites[i].code + "</a>";
****************************
    <a href="#" onclick="editDepenseMission('{{ depense.id }}','{{ depense.date|date('Y-m-d') }}','{{ depense.depense.id}}','{{ depense.justificationDepense.id}}','{{ depense.montant }}','{{ depense.obs}}');return false;"> 



 $builder->add('name', 'text', array('constraints' => array(new NotBlank(array('message' => 'Name cannot be blank')), new Length(array('min' => 3, 'minMessage' => 'Name is too short')), 'label' => 'Name')))
 $builder->add('name', 'text', array('constraints' => array(new NotBlank(array('message' => 'Name cannot be blank')), new Length(array('min' => 3, 'minMessage' => 'Name is too short'))), 'label' => 'Name'))    