<?php

class PDF extends FPDF
{

function setPied($New_Indice1){
     $this->pied = $New_Indice1;
}
function setDate($New_Indice2){
     $this->date = $New_Indice2;
}
function setRecuNumber($New_Indice3){
     $this->RecuNumber = $New_Indice3;
}
function setClientAdresse($New_Indice4){
     $this->clientadresse = $New_Indice4;
}
function setCommentaire($New_Indice5){
     $this->Commentaire = $New_Indice5;
}
function setlogo($New_Indice6){
     $this->logo = $New_Indice6;
}
function setTailleText($New_Indice7){
     $this->TailleText = $New_Indice7;
}
function setWidth($New_Indice8){
     $this->Width = $New_Indice8;
}
function setTypo($New_Indice9){
     $this->Typo = $New_Indice9;
}
function setColorText($New_Indice10){
     $this->ColorText = $New_Indice10;
}
function setColorText2($New_Indice11){
     $this->ColorText2 = $New_Indice11;
}
function setColorFont1($New_Indice12){
     $this->ColorFont1 = $New_Indice12;
}
function setColorFont2($New_Indice13){
     $this->ColorFont2 = $New_Indice13;
}
function setColorFont3($New_Indice14){
     $this->ColorFont3 = $New_Indice14;
}
function setType($New_Indice15){
     $this->type = $New_Indice15;
}
function setAdresse($New_Indice16){
     $this->Adresse = $New_Indice16;
}
function setMotif($New_Indice17){
     $this->Motif = $New_Indice17;
}
function setMontant($New_Indice18){
     $this->Montant = $New_Indice18;
}
function setLettre($New_Indice18){
     $this->Lettre = $New_Indice18;
}
function setMode($New_Indice19){
     $this->Mode = $New_Indice19;
}
function setSociete($New_Indice20){
     $this->Societe = $New_Indice20;
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
    $this->Cell(90,8, "Reçu de paiement", 1, 0, "C");

    $this->Ln();

    $this->SetTextColor($this->ColorText);
    $this->SetX( 110 );
    $this->SetFont( $this->Typo, "B", $this->TailleText);
    $this->Cell(45, $this->Width, "N° Reçu", 1, 0, "C",1);

    $this->SetX( 155);
    $this->SetFont( $this->Typo, "B", $this->TailleText);
    $this->Cell(45, $this->Width, "Date de paiement", 1, 0, "C",1);

    $this->Ln();

    $this->SetTextColor($this->ColorText2);
    $this->SetX( 110);
    $this->SetFont( $this->Typo, "", $this->TailleText);
    $this->Cell(45, $this->Width,$this->RecuNumber, 1,0, "C");

    $this->SetX( 155);
    $this->SetFont( $this->Typo, "", $this->TailleText);
    $this->Cell(45, $this->Width,$this->date, 1,0, "C");

// adresse

if ($this->type=="1") {
    $this->SetXY( 10, 35);
}
else {
    $this->SetXY( 10, 10);
}
    $this->MultiCell( 90, $this->Width, $this->Adresse);

    $this->SetFont( $this->Typo, "", $this->TailleText);
    $this->SetXY( 110, 40);
    $this->MultiCell( 90, $this->Width, $this->clientadresse);

 // tableau central

  $this->Ln(10);
  $this->SetX( 10);
  $this->MultiCell( 180, $this->Width, $this->Societe." a reçu la somme de ".$this->Montant." ( ".$this->Lettre." ),");
  $this->Ln(1);
  $this->MultiCell( 180, $this->Width, "pour de motif suivant : ".$this->Motif);
  $this->Ln(1);
  $this->MultiCell( 180, $this->Width, "Mode de paiement : ".$this->Mode);
  $this->Ln(1);
  $this->MultiCell( 180, $this->Width, "Commentaire : ".$this->Commentaire);
  $this->Ln(5);
  $this->SetX( 150);
  $this->Cell( 40, $this->Width, "Signature ");

}


//--------------------

// Pied de page
function Footer()
{
    // Positionnement à 1,5 cm du bas
    $this->SetXY(-15, -15);
    // Police Arial italique 8
    $this->SetFont($this->Typo,'I',8);
    // Numéro de page
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');

    // Positionnement à 1,5 cm du bas
    $this->SetY(-15);
    // Police Arial italique 8
    $this->SetFont($this->Typo,'I',8);
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
    if($i==0){
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