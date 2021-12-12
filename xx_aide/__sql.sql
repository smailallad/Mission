SELECT * FROM site WHERE code_site REGEXP '^A';

SELECT * FROM site WHERE code_site REGEXP '^[aco][0-9][x]' 
SELECT * FROM site WHERE code_site REGEXP '^[aco][0-9]{2}[x]' 
SELECT * FROM site WHERE code_site REGEXP '^[aco][0-9]{2}[^s]' 
SELECT * FROM site WHERE code_site REGEXP '^[aco][0-9]{2}[^sxm]' 
SELECT * FROM site WHERE code_site REGEXP '^[aco][0-9]{2}[sxmbt]' 


// OTA *************************************************************************
SELECT * FROM site WHERE (code_site          REGEXP '^[aco][0-9]{2}[sxmbt]|^[a-z \-]+$|^msc[0-9]+$' or code_site = 'WH OTA' or code site = 'WH-OTA Constantine' ) and ( client =0 )
UPDATE site SET client = 1 WHERE (code_site  REGEXP '^[aco][0-9]{2}[sxmbt]|^[a-z \-]+$|^msc[0-9]+$' or code_site = 'WH OTA' or code site = 'WH-OTA Constantine' ) and ( client =0 )

SELECT * FROM site WHERE (code_site REGEXP '^[a-z \-]+$' ) and ( client =0 ) 
UPDATE site SET client = 1 WHERE (code_site REGEXP '^[a-z \-]+$' ) and ( client =0 )

SELECT * FROM site WHERE (code_site REGEXP '^msc[0-9]+$' ) and ( client =0 )
UPDATE site SET client = 1 WHERE (code_site REGEXP '^msc[0-9]+$' ) and ( client =0 )

// ATM *************************************************************************
SELECT * FROM site WHERE (code_site REGEXP '^[0-9]+$' ) and ( client =0 )
UPDATE site SET client = 2 WHERE (code_site REGEXP '^[0-9]+$' or code_site = 'PY' ) and ( client =0 )

// AT **************************************************************************
SELECT * FROM site WHERE (code_site          REGEXP '^ct|^ca' or code_site ='Hydra mobilis' or code_site = 'WH AT' ) and ( client =0 )
UPDATE site SET client = 3 WHERE (code_site  REGEXP '^ct|^ca' or code_site ='Hydra mobilis' or code_site = 'WH AT' ) and ( client =0 )

SELECT * FROM site WHERE (code_site REGEXP '^ca' ) and ( client =0 )
UPDATE site SET client = 3 WHERE (code_site REGEXP '^ca' ) and ( client =0 )

// Orredoo ********************************************************************
SELECT * FROM site WHERE (code_site          REGEXP '^al|^ans|^bas|^to|^tp|^ts|^bat[0-9]+|^sos[0-9]+$|^se[0-9]+$|^sks[0-9]+$|^ai[0-9]+$|^bj|^bl|^bm|^bo|^ch|^et|^gu|^kh|^ms[0-9]+|^ob' or code_site = 'Boutique Orredoo' or code_site ='Boutique orredoo bej' or code_site = 'WH WTA Oran' or code_site = 'WH WTA Rouiba' ) and ( client =0 )
UPDATE site SET client = 4 WHERE (code_site  REGEXP '^al|^ans|^bas|^to|^tp|^ts|^bat[0-9]+|^sos[0-9]+$|^se[0-9]+$|^sks[0-9]+$|^ai[0-9]+$|^bj|^bl|^bm|^bo|^ch|^et|^gu|^kh|^ms[0-9]+|^ob' or code_site = 'Boutique Orredoo' or code_site ='Boutique orredoo bej' or code_site = 'WH WTA Oran' or code_site = 'WH WTA Rouiba' ) and ( client =0 )

SELECT * FROM site WHERE (code_site REGEXP '^bj|^bl|^bm|^bo|^ch|^et|^gu|^kh|^ms[0-9]+|^ob' ) and ( client =0 )
UPDATE site SET client = 4 WHERE (code_site  REGEXP '^bj|^bl|^bm|^bo|^ch|^et|^gu|^kh|^ms[0-9]+|^ob' ) and ( client =0 )

// CITAL ***********************************************************************
SELECT * FROM site WHERE (code_site          REGEXP '^p[0-9]+$|^sst[0-9]+$' or code_site = 'LSI du depot' or code_site = 'LTPCC' ) and ( client =0 )
UPDATE site SET client = 5 WHERE (code_site  REGEXP '^p[0-9]+$|^sst[0-9]+$' or code_site = 'LSI du depot' or code_site = 'LTPCC' ) and ( client =0 )

// ELR1 ************************************************************************
SELECT * FROM site WHERE (code_site          REGEXP 'elr1' ) and ( client =0 )
UPDATE site SET client = 6 WHERE (code_site  REGEXP 'elr1' ) and ( client =0 )

// ELR1 ************************************************************************
SELECT * FROM site WHERE            (code_site = 'centrale electrique' ) and ( client =0 )
UPDATE site SET client = 7 WHERE    (code_site = 'centrale electrique' ) and ( client =0 )
// Suppression *****************************************************************



UPDATE site SET client = 1 WHERE (client=0)

DELETE FROM `site` WHERE code_site = 'E1C'

Alcatel
RTIE
LTPCC
ZCINA
PY
CINA
AOP
Boutique Orredoo
Boutique orredoo bej	
centrale electrique 
Hydra mobilis
LSI du depot
Siege CFAO
WH AT
WH OTA
WH WTA Oran
WH WTA Rouiba
WH-OTA Constantine

********************************

     ALTER TABLE carburant CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE groupes CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE wilaya CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE depense CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE facture CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE prestation CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE roles CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE justification_depense CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE client CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE user CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE zone CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE fonction CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE projet CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE intervention CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE table_frais_mission CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE sous_projet CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE vehicule CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE site CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE famille_depense CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE mission CHANGE id id INT AUTO_INCREMENT NOT NULL;


     ALTER TABLE intervention_user ADD CONSTRAINT FK_822CCE8B8EAE3863 FOREIGN KEY (intervention_id) REFERENCES intervention (id);
     ALTER TABLE intervention_user ADD CONSTRAINT FK_822CCE8BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id);
     ALTER TABLE tarif_prestation ADD CONSTRAINT FK_258A4F9E45C554 FOREIGN KEY (prestation_id) REFERENCES prestation (id);
     ALTER TABLE tarif_prestation ADD CONSTRAINT FK_258A4F9F2C3FAB FOREIGN KEY (zone_id) REFERENCES zone (id);
     ALTER TABLE wilaya ADD CONSTRAINT FK_CF6AF33B9F2C3FAB FOREIGN KEY (zone_id) REFERENCES zone (id);
     ALTER TABLE depense ADD CONSTRAINT FK_34059757B52C88C9 FOREIGN KEY (famille_depense_id) REFERENCES famille_depense (id);
     ALTER TABLE prestation ADD CONSTRAINT FK_51C88FAD1BC8693D FOREIGN KEY (sous_projet_id) REFERENCES sous_projet (id);
     ALTER TABLE fonction_user ADD CONSTRAINT FK_F8A99815A76ED395 FOREIGN KEY (user_id) REFERENCES user (id);
     ALTER TABLE fonction_user ADD CONSTRAINT FK_F8A9981557889920 FOREIGN KEY (fonction_id) REFERENCES fonction (id);
     ALTER TABLE depense_mission ADD CONSTRAINT FK_95CF2ED941D81563 FOREIGN KEY (depense_id) REFERENCES depense (id);
     ALTER TABLE depense_mission ADD CONSTRAINT FK_95CF2ED9BE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id);
     ALTER TABLE depense_mission ADD CONSTRAINT FK_95CF2ED983CCA94D FOREIGN KEY (justification_depense_id) REFERENCES justification_depense (id);
     ALTER TABLE recrutement ADD CONSTRAINT FK_25EB2319A76ED395 FOREIGN KEY (user_id) REFERENCES user (id);
     ALTER TABLE carburant_mission ADD CONSTRAINT FK_A04C32AEBE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id);
     ALTER TABLE carburant_mission ADD CONSTRAINT FK_A04C32AE4A4A3511 FOREIGN KEY (vehicule_id) REFERENCES vehicule (id);
     ALTER TABLE carburant_mission ADD CONSTRAINT FK_A04C32AE32DAAD24 FOREIGN KEY (carburant_id) REFERENCES carburant (id);
     ALTER TABLE carburant_mission ADD CONSTRAINT FK_A04C32AE83CCA94D FOREIGN KEY (justification_depense_id) REFERENCES justification_depense (id);
     ALTER TABLE frais_mission ADD CONSTRAINT FK_9D980910BE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id);
     ALTER TABLE frais_mission ADD CONSTRAINT FK_9D980910A76ED395 FOREIGN KEY (user_id) REFERENCES user (id);
     ALTER TABLE user ADD CONSTRAINT FK_8D93D649305371B FOREIGN KEY (groupes_id) REFERENCES groupes (id);
     ALTER TABLE intervention ADD CONSTRAINT FK_D11814ABBE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id);
     ALTER TABLE intervention ADD CONSTRAINT FK_D11814ABF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id);
     ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB9E45C554 FOREIGN KEY (prestation_id) REFERENCES prestation (id);
     ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB4A4A3511 FOREIGN KEY (vehicule_id) REFERENCES vehicule (id);
     ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB7F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id);
     ALTER TABLE table_frais_mission ADD CONSTRAINT FK_E847491DDC89F5B6 FOREIGN KEY (wilaya_id) REFERENCES wilaya (id);
     ALTER TABLE sous_projet ADD CONSTRAINT FK_800A6E5EC18272 FOREIGN KEY (projet_id) REFERENCES projet (id);
     ALTER TABLE site ADD CONSTRAINT FK_694309E4DC89F5B6 FOREIGN KEY (wilaya_id) REFERENCES wilaya (id);
     ALTER TABLE site ADD CONSTRAINT FK_694309E419EB6921 FOREIGN KEY (client_id) REFERENCES client (id);
     ALTER TABLE mission ADD CONSTRAINT FK_9067F23CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id);
     ALTER TABLE licenciement ADD CONSTRAINT FK_7B60A384FCC7117B FOREIGN KEY (recrutement_id) REFERENCES recrutement (id);


==========================
$q=$this->_em->createQuery(
            "SELECT v.id,v.nom,date_diff(max(a.dateFin),:d) as nbrjours 
            FROM AppBundle:Assurance a 
            JOIN a.vehicule v 
            WHERE (v.active=1) 
            GROUP BY v.id
            ORDER BY v.nom");
        $q->setParameter('d',$d);
        return $q->getResult();

=======================

$connection = $this->_em->getConnection();

        $statement = $connection->prepare("
            SELECT * 
            FROM intervention_vehicule 
            CROSS JOIN marque
            WHERE important = 1
            AND (marque.id,intervention_vehicule.id) NOT IN(
                SELECT kms_intervention_vehicule.marque_id,kms_intervention_vehicule.intervention_vehicule_id
                FROM kms_intervention_vehicule
                ORDER BY kms_intervention_vehicule.marque_id,kms_intervention_vehicule.intervention_vehicule_id)
                
        ");

        $statement->execute();

        return $statement->fetchAll();

=========================
