<?php

class PDF extends FPDF
{
function setPied($New_Indice1){
     $this->pied = $New_Indice1;
}
function setDate($New_Indice2){
     $this->date = $New_Indice2;
}
function setDevisNumber($New_Indice3){
     $this->devisnumber = $New_Indice3;
}
function setClient($New_Indice4){
     $this->client = $New_Indice4;
}
function setClientAdresse($New_Indice5){
     $this->clientadresse = $New_Indice5;
}
function setEcheance($New_Indice6){
     $this->echeance = $New_Indice6;
}
function setNumTVA($New_Indice7){
     $this->numtva = $New_Indice7;
}
function setRemarque($New_Indice8){
     $this->Remarque = $New_Indice8;
}
function setTotalHT($New_Indice12){
     $this->TotalHT = $New_Indice12;
}
function setTotalTVA($New_Indice13){
     $this->TotalTVA = $New_Indice13;
}
function setTotalTTC($New_Indice14){
     $this->TotalTTC = $New_Indice14;
}
function setlogo($New_Indice15){
     $this->logo = $New_Indice15;
}
function setTailleText($New_Indice16){
     $this->TailleText = $New_Indice16;
}
function setWidth($New_Indice17){
     $this->Width = $New_Indice17;
}
function setTypo($New_Indice17){
     $this->Typo = $New_Indice17;
}
function setColorText($New_Indice18){
     $this->ColorText = $New_Indice18;
}
function setColorText2($New_Indice19){
     $this->ColorText2 = $New_Indice19;
}
function setColorFont1($New_Indice20){
     $this->ColorFont1 = $New_Indice20;
}
function setPenalite($New_Indice21){
     $this->Penalite = $New_Indice21;
}
function setEscompte($New_Indice22){
     $this->Escompte = $New_Indice22;
}
function setAccompte($New_Indice24){
     $this->Accompte = $New_Indice24;
}
function setAccompteE($New_Indice25){
     $this->AccompteE = $New_Indice25;
}
function setColorFont2($New_Indice26){
     $this->ColorFont2 = $New_Indice26;
}
function setColorFont3($New_Indice27){
     $this->ColorFont3 = $New_Indice27;
}
function setDebut($New_Indice28){
     $this->debut = $New_Indice28;
}
function setDuree($New_Indice29){
     $this->duree = $New_Indice29;
}
function setType($New_Indice30){
     $this->type = $New_Indice30;
}
function setAdresse($New_Indice31){
     $this->Adresse = $New_Indice31;
}
function setAE($New_Indice32){
     $this->AE = $New_Indice32;
}
function setAcompteActif($New_Indice33){
     $this->AcompteActif = $New_Indice33;
}

// En-tête
function Header()
{
if ($this->type=="1") {
        $this->Image($this->logo,10,10,'JPEG');
    }

    $this->SetFillColor($this->ColorFont1, $this->ColorFont2, $this->ColorFont3);

//Cadre Haut droite

    $this->SetXY( 110, 10 );
    $this->SetFont( $this->Typo, "B", $this->TailleText);
    $this->Cell(90,8, "DEVIS GRATUIT", 1, 0, "C");

    $this->Ln();

    $this->SetTextColor($this->ColorText);
    $this->SetX( 110 );
    $this->SetFont( $this->Typo, "B", $this->TailleText);
    $this->Cell(30, $this->Width, "N° Devis", 1, 0, "C",1);

    $this->SetX( 140);
    $this->SetFont( $this->Typo, "B", $this->TailleText);
    $this->Cell(30, $this->Width, "Date", 1, 0, "C",1);

    $this->SetX( 170);
    $this->SetFont( $this->Typo, "B", $this->TailleText);
    $this->Cell(30, $this->Width, "Code client", 1, 0, "C",1);

    $this->Ln();

    $this->SetTextColor($this->ColorText2);
    $this->SetX( 110);
    $this->SetFont( $this->Typo, "", $this->TailleText);
    $this->Cell(30, $this->Width,$this->devisnumber, 1,0, "C");

    $this->SetX( 140);
    $this->SetFont( $this->Typo, "", $this->TailleText);
    $this->Cell(30, $this->Width,$this->date, 1,0, "C");

    $this->SetX( 170);
    $this->SetFont( $this->Typo, "", $this->TailleText);
    $this->Cell(30, $this->Width,$this->client, 1,0, "C");

// adresse
if ($this->type=="1") {
    $this->SetXY( 10, 35);
}
else {
    $this->SetXY( 10, 10);
}
    $this->MultiCell( 90, $this->Width, $this->Adresse);

    if ($this->AE=="1") {
      $this->SetFont( $this->Typo, "", $this->TailleText-3);
        $this->MultiCell(90, 4, "Dispensé d'immatriculation aux registre du commerce \n et des société (RCS) et du répertoire des métiers (RM)");
    }

  $this->SetFont( $this->Typo, "", $this->TailleText);
    $this->SetXY( 110, 50);
    $this->MultiCell( 90, $this->Width, $this->clientadresse);

    $this->SetTextColor($this->ColorText);
    $this->SetXY( 10 , 80 );
    $this->SetFont( $this->Typo, "B", $this->TailleText);
    $this->Cell(45,$this->Width, "Date d'échéance", 1, 0, "C",1);

    $this->Ln();

    $this->SetTextColor($this->ColorText2);
    $this->SetX( 10 );
    $this->SetFont( $this->Typo, "", $this->TailleText);
    $this->Cell(45,$this->Width,$this->echeance, 1,0, "C");

    if (!empty($this->numtva)) {
        $this->SetTextColor($this->ColorText);
        $this->SetFont( $this->Typo, "B", $this->TailleText);
        $this->SetXY( 140 , 80 );
        $this->Cell(60, $this->Width, "TVA Intracommunautaire", 1, 0, "C",1);

        $this->Ln();

        $this->SetTextColor($this->ColorText2);
        $this->SetFont( $this->Typo, "", $this->TailleText);
        $this->SetX(140 );
        $this->Cell(60, $this->Width, $this->numtva, 1, 0, "C");
    }

 // tableau 
    $this->Ln();
    $this->SetTextColor($this->ColorText);
    $this->SetFont( $this->Typo, "B", $this->TailleText);

    $this->SetXY( 10 , 100 );
    $this->Cell(10, $this->Width, "ID", 1, 0, "C",1);

    $this->SetXY( 20 , 100 );
    $this->Cell(90, $this->Width, "Description", 1, 0, "C",1);

    $this->SetXY( 100 , 100 );
    $this->Cell(20, $this->Width, "Quantité", 1, 0, "C",1);

    $this->SetXY( 120 , 100 );
    $this->Cell(30, $this->Width, "PU HT", 1, 0, "C",1);

    $this->SetXY( 150 , 100 );
    $this->Cell(30, $this->Width, "Total HT", 1, 0, "C",1);

    $this->SetXY( 180 , 100 );
    $this->Cell(20, $this->Width, "Taux TVA", 1, 0, "C",1);
    $this->Ln();
}

//--------------------


function Bas() {
  $this->SetX( 10 );
    $this->SetFont( $this->Typo, "", 8);
    $this->Cell(35, $this->Width, "Pénalités de retard au taux annuel de : ".$this->Penalite." % - Escompte en cas de paiement anticipé : ".$this->Escompte, 0, 0, "L");

  $Y=$this->GetY();
  if ($Y >= 180) {
  $this->AddPage();
  }

//Bas 1ere ligne gauche
  $this->Ln(10);
    $this->SetTextColor($this->ColorText);
    $this->SetFillColor($this->ColorFont1, $this->ColorFont2, $this->ColorFont3);
    $this->SetFont( $this->Typo, "B", $this->TailleText);

if ($this->AcompteActif!=0) {
    $this->SetX( 10  );
    $this->Cell(40, $this->Width, "Accompte", 1, 0, "C",1);
}

    $this->SetX( 95  );
    $this->Cell(35, $this->Width, "Total HT", 1, 0, "C",1);

    $this->SetX( 130 );
    $this->Cell(35, $this->Width, "Total TVA", 1, 0, "C",1);

    $this->SetX( 165 );
    $this->Cell(35, $this->Width, "Total TTC", 1, 0, "C",1);

    $this->Ln();

// 2
    $this->SetTextColor($this->ColorText2);
  $this->SetFont( $this->Typo, "", $this->TailleText);

if ($this->AcompteActif!=0) {
    $this->SetX( 10 );
    $this->Cell(40, $this->Width, $this->AccompteE, 1, 0, "C");
}

    $this->SetX( 95 );
    $this->Cell(35, $this->Width, $this->TotalHT, 1, 0, "C");

    $this->SetX( 130 );
    $this->Cell(35, $this->Width, $this->TotalTVA, 1, 0, "C");

    $this->SetX( 165 );
    $this->Cell(35, $this->Width, $this->TotalTTC, 1, 0, "C");

if ($this->AE=="1") {
  $this->Ln();
  $this->SetX( 95 );
  $this->SetFont( $this->Typo, "", $this->TailleText-3);
  $this->Cell(105, $this->Width, "TVA non applicable, art.293-B du CGI", 0, 0, "R");
}

  $this->Ln(10);

  $this->SetX( 10 );
  $this->MultiCell( 190, $this->Width, $this->Remarque);

    $this->Ln(10);

    $this->SetXY( 10, 210 );
  $this->SetFont( $this->Typo, "", $this->TailleText-2);
    $this->Cell(180, $this->Width, "Date de debut de la prestation : ".$this->debut,0,0,"L");
  $this->Ln();
    $this->Cell(180, $this->Width, "Durée estimée de la prestation : ".$this->duree,0,0,"L");
  $this->Ln(10);
    $this->Cell(200, 5, "Devis reçu avant l'exécution des travaux", 0, 0, "L");
  $this->Ln();
  $this->SetFont( $this->Typo, "", $this->TailleText);
    $this->Cell(200, 5, 'Cachet et signature précédés de la mention " BON POUR ACCORD "', 0, 0, "L");

}
//--------------------

// Pied de page
function Footer()
{
    // Positionnement à 1,5 cm du bas
    $this->SetXY(-15, -15);
    // Police Arial italique 8
    $this->SetFont($this->Typo,'I',$this->TailleText-4);
    // Numéro de page
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');

    // Positionnement à 1,5 cm du bas
    $this->SetY(-15);
    // Police Arial italique 8
    // Numéro de page
    $this->Cell(0,10, $this->pied,0,0,'C');
}

var $widths;
var $aligns;
function SetWidths($w)
{
    //Tableau des largeurs de colonnes
    $this->widths=$w;
}

function SetAligns($a)
{
    //Tableau des alignements de colonnes
    $this->aligns=$a;
}

function Row($data)
{
    //Calcule la hauteur de la ligne
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h= $this->Width*$nb;
    //Effectue un saut de page si nécessaire
    $this->CheckPageBreak($h);
    //Dessine les cellules
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
    if($i==1){
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
    }
    else {
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
    }
        //Sauve la position courante
        $x=$this->GetX();
        $y=$this->GetY();
        //Dessine le cadre
        $this->Rect($x,$y,$w,$h);
        //Imprime le texte
        $this->MultiCell($w, $this->Width,$data[$i],0,$a);
        //Repositionne à droite
        $this->SetXY($x+$w,$y);
    }
    //Va à la ligne
    $this->Ln($h);
}

function CheckPageBreak($h)
{
    //Si la hauteur h provoque un débordement, saut de page manuel
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt)
{
    //Calcule le nombre de lignes qu'occupe un MultiCell de largeur w
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}

}

?>