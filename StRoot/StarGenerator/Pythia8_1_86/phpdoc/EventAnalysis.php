<html>
<head>
<title>Event Analysis</title>
<link rel="stylesheet" type="text/css" href="pythia.css"/>
<link rel="shortcut icon" href="pythia32.gif"/>
</head>
<body>

<script language=javascript type=text/javascript>
function stopRKey(evt) {
var evt = (evt) ? evt : ((event) ? event : null);
var node = (evt.target) ? evt.target :((evt.srcElement) ? evt.srcElement : null);
if ((evt.keyCode == 13) && (node.type=="text"))
{return false;}
}

document.onkeypress = stopRKey;
</script>
<?php
if($_POST['saved'] == 1) {
if($_POST['filepath'] != "files/") {
echo "<font color='red'>SETTINGS SAVED TO FILE</font><br/><br/>"; }
else {
echo "<font color='red'>NO FILE SELECTED YET.. PLEASE DO SO </font><a href='SaveSettings.php'>HERE</a><br/><br/>"; }
}
?>

<form method='post' action='EventAnalysis.php'>
 
<h2>Event Analysis</h2> 
 
<h3>Introduction</h3> 
 
The routines in this section are intended to be used to analyze 
event properties. As such they are not part of the main event 
generation chain, but can be used in comparisons between Monte 
Carlo events and real data. They are rather free-standing, but 
assume that input is provided in the PYTHIA 8 
<code>Event</code> format, and use a few basic facilities such 
as four-vectors. Their ordering is mainly by history; for current 
LHC applications the final one, <code>SlowJet</code>, is of 
special interest. 
 
<p/> 
In addition to the methods presented here, there is also the 
possibility to make use of <?php $filepath = $_GET["filepath"];
echo "<a href='JetFinders.php?filepath=".$filepath."' target='page'>";?>external 
jet finders </a>. 
 
<h3>Sphericity</h3> 
 
The standard sphericity tensor is 
<br/><i> 
    S^{ab} = (sum_i p_i^a p_i^b) / (sum_i p_i^2) 
</i><br/> 
where the <i>sum i</i> runs over the particles in the event, 
<i>a, b = x, y, z,</i> and <i>p</i> without such an index is 
the absolute size of the three-momentum . This tensor can be 
diagonalized to find eigenvalues and eigenvectors. 
 
<p/> 
The above tensor can be generalized by introducing a power 
<i>r</i>, such that 
<br/><i> 
    S^{ab} = (sum_i p_i^a p_i^b p_i^{r-2}) / (sum_i p_i^r) 
</i><br/> 
In particular, <i>r = 1</i> gives a linear dependence on momenta 
and thus a collinear safe definition, unlike sphericity. 
 
<p/> 
To do sphericity analyses you have to set up a <code>Sphericity</code> 
instance, and then feed in events to it, one at a time. The results 
for the latest event are available as output from a few methods. 
 
<a name="method1"></a>
<p/><strong>Sphericity::Sphericity(double power = 2., int select = 2) &nbsp;</strong> <br/>
create a sphericity analysis object, where 
<br/><code>argument</code><strong> power </strong> (<code>default = <strong>2.</strong></code>) :  
is the power <i>r</i> defined above, i.e. 
<br/><code>argumentoption </code><strong> 2.</strong> : gives Sphericity, and   
<br/><code>argumentoption </code><strong> 1.</strong> : gives the linear form.   
   
<br/><code>argument</code><strong> select </strong> (<code>default = <strong>2</strong></code>) :  
tells which particles are analyzed, 
<br/><code>argumentoption </code><strong> 1</strong> : all final-state particles,   
<br/><code>argumentoption </code><strong> 2</strong> : all observable final-state particles, 
i.e. excluding neutrinos and other particles without strong or 
electromagnetic interactions (the <code>isVisible()</code> 
particle method), and 
   
<br/><code>argumentoption </code><strong> 3</strong> : only charged final-state particles.   
   
   
 
<a name="method2"></a>
<p/><strong>bool Sphericity::analyze( const Event& event, ostream& os = cout) &nbsp;</strong> <br/>
perform a sphericity analysis, where 
<br/><code>argument</code><strong> event </strong>  : is an object of the <code>Event</code> class, 
most likely the <code>pythia.event</code> one. 
   
<br/><code>argument</code><strong> os </strong> (<code>default = <strong>cout</strong></code>) :  is the output stream for 
error messages. (The method does not rely on the <code>Info</code> 
machinery for error messages.) 
   
<br/>If the routine returns <code>false</code> the 
analysis failed, e.g. if too few particles are present to analyze. 
   
 
<p/> 
After the analysis has been performed, a few methods are available 
to return the result of the analysis of the latest event: 
 
<a name="method3"></a>
<p/><strong>double Sphericity::sphericity() &nbsp;</strong> <br/>
gives the sphericity (or equivalent if <i>r</i> is not 2), 
   
 
<a name="method4"></a>
<p/><strong>double Sphericity::aplanarity() &nbsp;</strong> <br/>
gives the aplanarity (with the same comment), 
   
 
<a name="method5"></a>
<p/><strong>double Sphericity::eigenValue(int i) &nbsp;</strong> <br/>
gives one of the three eigenvalues for <i>i</i> = 1, 2 or 3, in 
descending order, 
   
 
<a name="method6"></a>
<p/><strong>Vec4 Sphericity::eventAxis(i) &nbsp;</strong> <br/>
gives the matching normalized eigenvector, as a <code>Vec4</code> 
with vanishing time/energy component. 
   
 
<a name="method7"></a>
<p/><strong>void Sphericity::list(ostream& os = cout) &nbsp;</strong> <br/>
provides a listing of the above information. 
   
 
<p/> 
There is also one method that returns information accumulated for all 
the events analyzed so far. 
 
<a name="method8"></a>
<p/><strong>int Sphericity::nError() &nbsp;</strong> <br/>
tells the number of times <code>analyze(...)</code> failed to analyze 
events, i.e. returned <code>false</code>. 
   
 
<h3>Thrust</h3> 
 
Thrust is obtained by varying the thrust axis so that the longitudinal 
momentum component projected onto it is maximized, and thrust itself is 
then defined as the sum of absolute longitudinal momenta divided by 
the sum of absolute momenta. The major axis is found correspondingly 
in the plane transverse to thrust, and the minor one is then defined 
to be transverse to both. Oblateness is the difference between the major 
and the minor values. 
 
<p/> 
The calculation of thrust is more computer-time-intensive than e.g. 
linear sphericity, introduced above, and has no specific advantages except 
historical precedent. In the PYTHIA 6 implementation the search was 
sped up at the price of then not being guaranteed to hit the absolute 
maximum. The current implementation studies all possibilities, but at 
the price of being slower, with time consumption for an event with 
<i>n</i> particles growing like <i>n^3</i>. 
 
<p/> 
To do thrust analyses you have to set up a <code>Thrust</code> 
instance, and then feed in events to it, one at a time. The results 
for the latest event are available as output from a few methods. 
 
<a name="method9"></a>
<p/><strong>Thrust::Thrust(int select = 2) &nbsp;</strong> <br/>
create a thrust analysis object, where 
<br/><code>argument</code><strong> select </strong> (<code>default = <strong>2</strong></code>) :  
tells which particles are analyzed, 
<br/><code>argumentoption </code><strong> 1</strong> : all final-state particles,   
<br/><code>argumentoption </code><strong> 2</strong> : all observable final-state particles, 
i.e. excluding neutrinos and other particles without strong or 
electromagnetic interactions (the <code>isVisible()</code> 
particle method), and 
   
<br/><code>argumentoption </code><strong> 3</strong> : only charged final-state particles.   
   
   
 
<a name="method10"></a>
<p/><strong>bool Thrust::analyze( const Event& event, ostream& os = cout) &nbsp;</strong> <br/>
perform a thrust analysis, where 
<br/><code>argument</code><strong> event </strong>  : is an object of the <code>Event</code> class, 
most likely the <code>pythia.event</code> one. 
   
<br/><code>argument</code><strong> os </strong> (<code>default = <strong>cout</strong></code>) :  is the output stream for 
error messages. (The method does not rely on the <code>Info</code> 
machinery for error messages.) 
   
<br/>If the routine returns <code>false</code> the 
analysis failed, e.g. if too few particles are present to analyze. 
   
 
<p/> 
After the analysis has been performed, a few methods are available 
to return the result of the analysis of the latest event: 
 
<a name="method11"></a>
<p/><strong>double Thrust::thrust() &nbsp;</strong> <br/>
   
<strong>double Thrust::tMajor() &nbsp;</strong> <br/>
   
<strong>double Thrust::tMinor() &nbsp;</strong> <br/>
   
<strong>double Thrust::oblateness() &nbsp;</strong> <br/>
gives the thrust, major, minor and oblateness values, respectively, 
   
 
<a name="method12"></a>
<p/><strong>Vec4 Thrust::eventAxis(int i) &nbsp;</strong> <br/>
gives the matching normalized event-axis vectors, for <i>i</i> = 1, 2 or 3 
corresponding to thrust, major or minor, as a <code>Vec4</code> with 
vanishing time/energy component. 
   
 
<a name="method13"></a>
<p/><strong>void Thrust::list(ostream& os = cout) &nbsp;</strong> <br/>
provides a listing of the above information. 
   
 
<p/> 
There is also one method that returns information accumulated for all 
the events analyzed so far. 
 
<a name="method14"></a>
<p/><strong>int Thrust::nError() &nbsp;</strong> <br/>
tells the number of times <code>analyze(...)</code> failed to analyze 
events, i.e. returned <code>false</code>. 
   
 
<h3>ClusterJet</h3> 
 
<code>ClusterJet</code> (a.k.a. <code>LUCLUS</code> and 
<code>PYCLUS</code>) is a clustering algorithm of the type used for 
analyses of <i>e^+e^-</i> events, see the PYTHIA 6 manual. All 
visible particles in the events are clustered into jets. A few options 
are available for some well-known distance measures. Cutoff 
distances can either be given in terms of a scaled quadratic quantity 
like <i>y = pT^2/E^2</i> or an unscaled linear one like <i>pT</i>. 
 
<p/> 
Note that we have deliberately chosen not to include the <i>e^+e^-</i> 
equivalents of the Cambridge/Aachen and anti-<i>kRT</i> algorithms. 
These tend to be good at clustering the densely populated (in angle) 
cores of jets, but less successful for the sparsely populated transverse 
regions, where many jets may come to consist of a single low-momentum 
particle. In hadron collisions such jets could easily be disregarded, 
while in <i>e^+e^-</i> annihilation all particles derive back from the 
hard process. 
 
<p/> 
To do jet finding analyses you have to set up a <code>ClusterJet</code> 
instance, and then feed in events to it, one at a time. The results 
for the latest event are available as output from a few methods. 
 
<a name="method15"></a>
<p/><strong>ClusterJet::ClusterJet(string measure = &quot;Lund&quot;, int select = 2, int massSet = 2, bool precluster = false, bool reassign = false) &nbsp;</strong> <br/>
create a <code>ClusterJet</code> instance, where 
<br/><code>argument</code><strong> measure </strong> (<code>default = <strong>&quot;Lund&quot;</strong></code>) : distance measure, 
to be provided as a character string (actually, only the first character 
is necessary) 
<br/><code>argumentoption </code><strong> &quot;Lund&quot;</strong> : the Lund <i>pT</i> distance, 
   
<br/><code>argumentoption </code><strong> &quot;JADE&quot;</strong> : the JADE mass distance, and 
   
<br/><code>argumentoption </code><strong> &quot;Durham&quot;</strong> : the Durham <i>kT</i> measure. 
   
   
<br/><code>argument</code><strong> select </strong> (<code>default = <strong>2</strong></code>) :  
tells which particles are analyzed, 
<br/><code>argumentoption </code><strong> 1</strong> : all final-state particles,   
<br/><code>argumentoption </code><strong> 2</strong> : all observable final-state particles, 
i.e. excluding neutrinos and other particles without strong or 
electromagnetic interactions (the <code>isVisible()</code> particle 
method), and 
   
<br/><code>argumentoption </code><strong> 3</strong> : only charged final-state particles.   
   
<br/><code>argument</code><strong> massSet </strong> (<code>default = <strong>2</strong></code>) : masses assumed for the particles 
used in the analysis 
<br/><code>argumentoption </code><strong> 0</strong> : all massless,   
<br/><code>argumentoption </code><strong> 1</strong> : photons are massless while all others are 
assigned the <i>pi+-</i> mass, and 
   
<br/><code>argumentoption </code><strong> 2</strong> : all given their correct masses.   
   
<br/><code>argument</code><strong> precluster </strong> (<code>default = <strong>false</strong></code>) :  
perform or not a early preclustering step, where nearby particles 
are lumped together so as to speed up the subsequent normal clustering. 
   
<br/><code>argument</code><strong> reassign </strong> (<code>default = <strong>false</strong></code>) :  
reassign all particles to the nearest jet each time after two jets 
have been joined. 
   
   
 
<a name="method16"></a>
<p/><strong>ClusterJet::analyze( const Event& event, double yScale, double pTscale, int nJetMin = 1, int nJetMax = 0, ostream& os = cout) &nbsp;</strong> <br/>
performs a jet finding analysis, where 
<br/><code>argument</code><strong> event </strong>  : is an object of the <code>Event</code> class, 
most likely the <code>pythia.event</code> one. 
   
<br/><code>argument</code><strong> yScale </strong>  :  
is the cutoff joining scale, below which jets are joined. Is given 
in quadratic dimensionless quantities. Either <code>yScale</code> 
or <code>pTscale</code> must be set nonvanishing, and the larger of 
the two dictates the actual value. 
   
<br/><code>argument</code><strong> pTscale </strong>  :  
is the cutoff joining scale, below which jets are joined. Is given 
in linear quantities, such as <i>pT</i> or <i>m</i> depending on 
the measure used, but always in units of GeV. Either <code>yScale</code> 
or <code>pTscale</code> must be set nonvanishing, and the larger of 
the two dictates the actual value. 
   
<br/><code>argument</code><strong> nJetMin </strong> (<code>default = <strong>1</strong></code>) :  
the minimum number of jets to be reconstructed. If used, it can override 
the <code>yScale</code> and <code>pTscale</code> values. 
   
<br/><code>argument</code><strong> nJetMax </strong> (<code>default = <strong>0</strong></code>) :  
the maximum number of jets to be reconstructed. Is not used if below 
<code>nJetMin</code>. If used, it can override the <code>yScale</code> 
and <code>pTscale</code> values. Thus e.g. 
<code>nJetMin = nJetMax = 3</code> can be used to reconstruct exactly 
3 jets. 
   
<br/><code>argument</code><strong> os </strong> (<code>default = <strong>cout</strong></code>) :  is the output stream for 
error messages. (The method does not rely on the <code>Info</code> 
machinery for error messages.) 
   
<br/>If the routine returns <code>false</code> the analysis failed, 
e.g. because the number of particles was smaller than the minimum number 
of jets requested. 
   
 
<p/> 
After the analysis has been performed, a few <code>ClusterJet</code> 
class methods are available to return the result of the analysis: 
 
<a name="method17"></a>
<p/><strong>int ClusterJet::size() &nbsp;</strong> <br/>
gives the number of jets found, with jets numbered 0 through 
<code>size() - 1</code>. 
   
 
<a name="method18"></a>
<p/><strong>Vec4 ClusterJet::p(int i) &nbsp;</strong> <br/>
gives a <code>Vec4</code> corresponding to the four-momentum defined by 
the sum of all the contributing particles to the <i>i</i>'th jet. 
   
 
<a name="method19"></a>
<p/><strong>int ClusterJet::mult(int i) &nbsp;</strong> <br/>
the number of particles that have been clustered into the <i>i</i>'th jet. 
   
 
<a name="method20"></a>
<p/><strong>int ClusterJet::jetAssignment(int i) &nbsp;</strong> <br/>
gives the index of the jet that the particle <i>i</i> of the event 
record belongs to, 
   
 
<a name="method21"></a>
<p/><strong>void ClusterJet::list(ostream& os = cout) &nbsp;</strong> <br/>
provides a listing of the reconstructed jets. 
   
 
<a name="method22"></a>
<p/><strong>int ClusterJet::distanceSize() &nbsp;</strong> <br/>
the number of most recent clustering scales that have been stored 
for readout with the next method. Normally this would be five, 
but less if fewer clustering steps occurred. 
   
 
<a name="method23"></a>
<p/><strong>double ClusterJet::distance(int i) &nbsp;</strong> <br/>
clustering scales, with <code>distance(0)</code> being the most 
recent one, i.e. normally the highest, up to <code>distance(4)</code> 
being the fifth most recent. That is, with <i>n</i> being the final 
number of jets, <code>ClusterJet::size()</code>, the scales at which 
<i>n+1</i> jets become <i>n</i>, <i>n+2</i> become <i>n+1</i>, 
and so on till <i>n+5</i> become <i>n+4</i>. Nonexisting clustering 
scales are returned as zero. The physical interpretation of a scale is 
as provided by the respective distance measure (Lund, JADE, Durham). 
   
 
<p/> 
There is also one method that returns information accumulated for all 
the events analyzed so far. 
 
<a name="method24"></a>
<p/><strong>int ClusterJet::nError() &nbsp;</strong> <br/>
tells the number of times <code>analyze(...)</code> failed to analyze 
events, i.e. returned <code>false</code>. 
   
 
<h3>CellJet</h3> 
 
<code>CellJet</code> (a.k.a. <code>PYCELL</code>) is a simple cone jet 
finder in the UA1 spirit, see the PYTHIA 6 manual. It works in an 
<i>(eta, phi, eT)</i> space, where <i>eta</i> is pseudorapidity, 
<i>phi</i> azimuthal angle and <i>eT</i> transverse energy. 
It will draw cones in <i>R = sqrt(Delta-eta^2 + Delta-phi^2)</i> 
around seed cells. If the total <i>eT</i> inside the cone exceeds 
the threshold, a jet is formed, and the cells are removed from further 
analysis. There are no split or merge procedures, so later-found jets 
may be missing some of the edge regions already used up by previous 
ones. Not all particles in the event are assigned to jets; leftovers 
may be viewed as belonging to beam remnants or the underlying event. 
It is not used by any experimental collaboration, but is closely 
related to the more recent and better theoretically motivated 
anti-<i>kT</i> algorithm [<a href="Bibliography.php" target="page">Cac08</a>]. 
 
<p/> 
To do jet finding analyses you have to set up a <code>CellJet</code> 
instance, and then feed in events to it, one at a time. Especially note 
that, if you want to use the options where energies are smeared in 
order so emulate detector imperfections, you must hand in an external 
random number generator, preferably the one residing in the 
<code>Pythia</code> class. The results for the latest event are 
available as output from a few methods. 
 
<a name="method25"></a>
<p/><strong>CellJet::CellJet(double etaMax = 5., int nEta = 50, int nPhi = 32, int select = 2, int smear = 0, double resolution = 0.5, double upperCut = 2., double threshold = 0., Rndm* rndmPtr = 0) &nbsp;</strong> <br/>
create a <code>CellJet</code> instance, where 
<br/><code>argument</code><strong> etaMax </strong> (<code>default = <strong>5.</strong></code>) :  
the maximum +-pseudorapidity that the detector is assumed to cover. 
   
<br/><code>argument</code><strong> nEta </strong> (<code>default = <strong>50</strong></code>) :  
the number of equal-sized bins that the <i>+-etaMax</i> range 
is assumed to be divided into. 
   
<br/><code>argument</code><strong> nPhi </strong> (<code>default = <strong>32</strong></code>) :  
the number of equal-sized bins that the <i>phi</i> range 
<i>+-pi</i> is assumed to be divided into. 
   
<br/><code>argument</code><strong> select </strong> (<code>default = <strong>2</strong></code>) :  
tells which particles are analyzed, 
<br/><code>argumentoption </code><strong> 1</strong> : all final-state particles,   
<br/><code>argumentoption </code><strong> 2</strong> : all observable final-state particles, 
i.e. excluding neutrinos and other particles without strong or 
electromagnetic interactions (the <code>isVisible()</code> particle 
method), 
and   
<br/><code>argumentoption </code><strong> 3</strong> : only charged final-state particles.   
   
<br/><code>argument</code><strong> smear </strong> (<code>default = <strong>0</strong></code>) :  
strategy to smear the actual <i>eT</i> bin by bin, 
<br/><code>argumentoption </code><strong> 0</strong> : no smearing,   
<br/><code>argumentoption </code><strong> 1</strong> : smear the <i>eT</i> according to a Gaussian 
with width <i>resolution * sqrt(eT)</i>, with the Gaussian truncated 
at 0 and <i>upperCut * eT</i>,   
<br/><code>argumentoption </code><strong> 2</strong> : smear the <i>e = eT * cosh(eta)</i> according 
to a Gaussian with width <i>resolution * sqrt(e)</i>, with the 
Gaussian truncated at 0 and <i>upperCut * e</i>.   
   
<br/><code>argument</code><strong> resolution </strong> (<code>default = <strong>0.5</strong></code>) :  
see above. 
   
<br/><code>argument</code><strong> upperCut </strong> (<code>default = <strong>2.</strong></code>) :  
see above. 
   
<br/><code>argument</code><strong> threshold </strong> (<code>default = <strong>0 GeV</strong></code>) :  
completely neglect all bins with an <i>eT &lt; threshold</i>. 
   
<br/><code>argument</code><strong> rndmPtr </strong> (<code>default = <strong>0</strong></code>) :  
the random-number generator used to select the smearing described 
above. Must be handed in for smearing to be possible. If your 
<code>Pythia</code> class instance is named <code>pythia</code>, 
then <code>&pythia.rndm</code> would be the logical choice. 
   
   
 
<a name="method26"></a>
<p/><strong>bool CellJet::analyze( const Event& event, double eTjetMin = 20., double coneRadius = 0.7, double eTseed = 1.5, ostream& os = cout) &nbsp;</strong> <br/>
performs a jet finding analysis, where 
<br/><code>argument</code><strong> event </strong>  : is an object of the <code>Event</code> class, 
most likely the <code>pythia.event</code> one. 
   
<br/><code>argument</code><strong> eTjetMin </strong> (<code>default = <strong>20. GeV</strong></code>) :  
is the minimum transverse energy inside a cone for this to be 
accepted as a jet. 
   
<br/><code>argument</code><strong> coneRadius </strong> (<code>default = <strong>0.7</strong></code>) :  
 is the size of the cone in <i>(eta, phi)</i> space drawn around 
the geometric center of the jet. 
   
<br/><code>argument</code><strong> eTseed </strong> (<code>default = <strong>1.5 GeV</strong></code>) :  
the minimum <i>eT</i> in a cell for this to be acceptable as 
the trial center of a jet. 
   
<br/><code>argument</code><strong> os </strong> (<code>default = <strong>cout</strong></code>) :  is the output stream for 
error messages. (The method does not rely on the <code>Info</code> 
machinery for error messages.) 
   
<br/>If the routine returns <code>false</code> the analysis failed, 
but currently this is not foreseen ever to happen. 
   
 
<p/> 
After the analysis has been performed, a few <code>CellJet</code> 
class methods are available to return the result of the analysis: 
 
<a name="method27"></a>
<p/><strong>int CellJet::size() &nbsp;</strong> <br/>
gives the number of jets found, with jets numbered 0 through 
<code>size() - 1</code>, 
   
 
<a name="method28"></a>
<p/><strong>double CellJet::eT(i) &nbsp;</strong> <br/>
gives the <i>eT</i> of the <i>i</i>'th jet, where jets have been 
ordered with decreasing <i>eT</i> values, 
   
 
<a name="method29"></a>
<p/><strong>double CellJet::etaCenter(int i) &nbsp;</strong> <br/>
   
<strong>double CellJet::phiCenter(int i) &nbsp;</strong> <br/>
gives the <i>eta</i> and <i>phi</i> coordinates of the geometrical 
center of the <i>i</i>'th jet, 
   
 
<a name="method30"></a>
<p/><strong>double CellJet::etaWeighted(int i) &nbsp;</strong> <br/>
   
<strong>double CellJet::phiWeighted(int i) &nbsp;</strong> <br/>
gives the <i>eta</i> and <i>phi</i> coordinates of the 
<i>eT</i>-weighted center of the <i>i</i>'th jet, 
   
 
<a name="method31"></a>
<p/><strong>int CellJet::multiplicity(int i) &nbsp;</strong> <br/>
gives the number of particles clustered into the <i>i</i>'th jet, 
   
 
<a name="method32"></a>
<p/><strong>Vec4 CellJet::pMassless(int i) &nbsp;</strong> <br/>
gives a <code>Vec4</code> corresponding to the four-momentum defined 
by the <i>eT</i> and the weighted center of the <i>i</i>'th jet, 
   
 
<a name="method33"></a>
<p/><strong>Vec4 CellJet::pMassive(int i) &nbsp;</strong> <br/>
gives a <code>Vec4</code> corresponding to the four-momentum defined by 
the sum of all the contributing cells to the <i>i</i>'th jet, where 
each cell contributes a four-momentum as if all the <i>eT</i> is 
deposited in the center of the cell, 
   
 
<a name="method34"></a>
<p/><strong>double CellJet::m(int i) &nbsp;</strong> <br/>
gives the invariant mass of the <i>i</i>'th jet, defined by the 
<code>pMassive</code> above, 
   
 
<a name="method35"></a>
<p/><strong>void CellJet::list() &nbsp;</strong> <br/>
provides a listing of the above information (except <code>pMassless</code>, 
for reasons of space). 
   
 
<p/> 
There is also one method that returns information accumulated for all 
the events analyzed so far. 
<a name="method36"></a>
<p/><strong>int CellJet::nError() &nbsp;</strong> <br/>
tells the number of times <code>analyze(...)</code> failed to analyze 
events, i.e. returned <code>false</code>. 
   
 
<h3>SlowJet</h3> 
 
<code>SlowJet</code> is a simple program for doing jet finding according 
to either of the <i>kT</i>, anti-<i>kT</i>, and Cambridge/Aachen 
algorithms, in a cylindrical coordinate frame. The name is obviously 
an homage to the <code>FastJet</code> program [<a href="Bibliography.php" target="page">Cac06, Cac12</a>]. 
That package contains many more algorithms, with many more options, 
and, above all, is <i>much</i> faster. Therefore <code>SlowJet</code> 
is not so much intended for massive processing of data or Monte Carlo 
files as for simple first tests. Nevertheless, within the advertised 
capabilities of <code>SlowJet</code>, it has been checked to find 
identically the same jets as <code>FastJet</code>. The time consumption 
typically is around or below that to generate an LHC <i>pp</i> event 
in the first place, so is not prohibitive. But the time rises rapidly 
for large multiplicities, so obviously <code>SlowJet</code> can not 
be used for tricks like distributing a dense grid of pseudoparticles 
to be able to define jet areas, like <code>FastJet</code> can, and also 
not for events with much pileup or other noise. 
 
<p/> 
The recent introduction of <code>fjcore</code>, containing the core 
functionality of <code>FastJet</code> in a very much smaller package, 
has changed the conditions. It now is possible (even encouraged by the 
authors) to distribute the two <code>fjcore</code> files as part of the 
PYTHIA package. Therefore the <code>SlowJet</code> class doubles as a 
convenient front end to <code>fjcore</code>, managing the conversion 
back and forth between PYTHIA and <code>FastJet</code> variables. Some 
applications may still benefit from using the native codes, but the default 
now is to let <code>SlowJet</code> call on <code>fjcore</code> for the 
jet finding. 
 
<p/> 
The first step is to decide which particles should be included in the 
analysis, and with what four-momenta. The <code>SlowJet</code> constructor 
allows to pick a maximum pseudorapidity defined by the extent of the 
assumed detector, to pick among some standard options of which particles 
to analyze, and to allow for some standard mass assumptions, like that 
all charged particles have the pion mass. Obviously this is only a 
restricted set of possibilities. 
 
<p/> 
Full flexibility can be obtained by deriving from the base class 
<code>SlowJetHook</code> to write your own <code>include</code> method. 
This will be presented with one final-state particle at a time, and 
should return <code>true</code> for those particles that should be 
analyzed. It is also possible to return modified four-momenta and masses, 
to take into account detector smearing effects or particle identity 
misassignments, but you must respect <i>E^2 - p^2 = m^2</i>. 
 
<p/> 
Alternatively you can modify the event record itself, or a copy of it 
(if you want to keep the original intact). For instance, only final 
particles are considered in the analysis, i.e. particles with positive 
status code, so negative status code should then be assigned to those 
particles that you do not want to see analyzed. Again four-momenta and 
masses can be modified, subject to <i>E^2 - p^2 = m^2</i>. 
 
<p/> 
The jet reconstructions is then based on sequential recombination with 
progressive removal, using the <i>E</i> recombination scheme. To be 
more specific, the algorithm works as follows. 
<ol> 
<li>Each particle to be analyzed defines an original cluster. It has a 
well-defined four-momentum and mass at input. From this information the 
triplet <i>(pT, y, phi)</i> is calculated, i.e. the transverse momentum, 
rapidity and azimuthal angle of the cluster.</li> 
<li>Define distance measures of all clusters <i>i</i> to the beam 
<br/><i>d_iB = pT_i^2p</i><br/> 
and of all pairs <i>(i,j)</i> relative to each other 
<br/><i>d_ij = min( pT_i^2p, pT_j^2p) DeltaR_ij^2 / R^2 </i><br/> 
where 
<br/><i>DeltaR_ij^2 = (y_i - y_j)^2 + (phi_i - phi_j)^2.</i><br/> 
The jet algorithm is determined by the user-selected <i>p</i> value, 
where <i>p = -1</i> corresponds to the anti-<i>kT</i> one, 
<i>p = 0</i> to the Cambridge/Aachen one and <i>p = 1</i> to the 
<i>kT</i> one. Also <i>R</i> is chosen by the user, to give an 
approximate measure of the size of jets. However, note that jets need 
not have a circular shape in <i>(y, phi)</i> space, so <i>R</i> 
can not straight off be interpreted as a jet radius.</li> 
<li>Find the smallest of all <i>d_iB</i> and <i>d_ij</i>.</li> 
<li>If this is a <i>d_iB</i> then cluster <i>i</i> is removed from 
the clusters list and instead added to the jets list. 
Optionally, a <i>pTjetMin</i> requirement is imposed, where only 
clusters with <i>pT > pTjetMin</i> are added to the jets list. 
If so, some of the analyzed particles will not be part of any final 
jet.</li> 
<li>If instead the smallest measure is a <i>d_ij</i> then the 
four-momenta of the <i>i</i> and <i>j</i> clusters are added 
to define a single new cluster. Convert this four-momentum to a new 
<i>(pT, y, phi)</i> triplet and update the list of <i>d_iB</i> 
and <i>d_ij</i>.</li> 
<li>Return to step 3 until no clusters remain.</li> 
</ol> 
 
<p/> 
To do jet finding analyses you first have to set up a <code>SlowJet</code> 
instance, where the arguments of the constructor specifies the details 
of the subsequent analyses. Thereafter you can feed in events to it, 
one at a time, and have them analyzed by the <code>analyze</code> method. 
Information on the resulting jets can be extracted by a few different methods. 
The minimal procedure only requires one call per event to do the analysis. 
We will begin by presenting it, and only afterwards some extensions. 
 
<a name="method37"></a>
<p/><strong>SlowJet::SlowJet(int power, double R, double pTjetMin = 0., double etaMax = 25., int select = 2, int massSet = 2, SlowJetHook* sjHookPtr = 0, bool useFJcore = true, bool useStandardR = true) &nbsp;</strong> <br/>
create a <code>SlowJet</code> instance, where 
<br/><code>argument</code><strong> power </strong>  :  
tells (half) the power of the transverse-momentum dependence of the 
distance measure, 
<br/><code>argumentoption </code><strong> -1</strong> : the anti-<i>kT</i> algorithm,   
<br/><code>argumentoption </code><strong> 0</strong> : the Cambridge/Aachen algorithm, and   
<br/><code>argumentoption </code><strong> 1</strong> : the <i>kT</i> algorithm.   
   
<br/><code>argument</code><strong> R </strong>  :  
the <i>R</i> size parameter, which is crudely related to the radius of 
the jet cone in <i>(y, phi)</i> space around the center of the jet. 
   
<br/><code>argument</code><strong> pTjetMin </strong> (<code>default = <strong>0.0 GeV</strong></code>) :  
the minimum transverse momentum required for a cluster 
to become a jet. By default all clusters become jets, and therefore 
all analyzed particles are assigned to a jet. 
For comparisons with perturbative QCD, however, it is only meaningful 
to consider jets with a significant <i>pT</i>. 
   
<br/><code>argument</code><strong> etaMax </strong> (<code>default = <strong>25.</strong></code>) :  
the maximum +-pseudorapidity that the detector is assumed to cover. 
If you pick a value above 20 there is assumed to be full coverage 
(obviously only meaningful for theoretical studies). 
   
<br/><code>argument</code><strong> select </strong> (<code>default = <strong>2</strong></code>) :  
tells which particles are analyzed, 
<br/><code>argumentoption </code><strong> 1</strong> : all final-state particles,   
<br/><code>argumentoption </code><strong> 2</strong> : all observable final-state particles, 
i.e. excluding neutrinos and other particles without strong or 
electromagnetic interactions (the <code>isVisible()</code> particle 
method), 
and   
<br/><code>argumentoption </code><strong> 3</strong> : only charged final-state particles.   
   
<br/><code>argument</code><strong> massSet </strong> (<code>default = <strong>2</strong></code>) : masses assumed for the particles 
used in the analysis 
<br/><code>argumentoption </code><strong> 0</strong> : all massless,   
<br/><code>argumentoption </code><strong> 1</strong> : photons are massless while all others are 
assigned the <i>pi+-</i> mass, and 
   
<br/><code>argumentoption </code><strong> 2</strong> : all given their correct masses.   
   
<br/><code>argument</code><strong> sjHookPtr </strong> (<code>default = <strong>0</strong></code>) :  
gives the possibility to send in your own selection routine for which 
particles should be part of the analysis; see further below on the 
<code>SlowJetHook</code> class. If this pointer is sent in nonzero, 
<code>etaMax</code> and <code>massSet</code> are disregarded, 
and <code>select</code> only gives the basic selection, to which 
the user can add further requirements. 
   
<br/><code>argument</code><strong> useFJcore </strong> (<code>default = <strong>true</strong></code>) : choice of code used for finding 
the jets. Does not affect the outcome of the analysis, but only the speed, 
and some more specialized options. 
<br/><code>argumentoption </code><strong> true</strong> : use the <code>fjcore</code> package of 
<code>FastJet 3.0.5</code>. 
   
<br/><code>argumentoption </code><strong> false</strong> : use the native <code>SlowJet</code> implementation, 
which gives a slower jet finding, but allows some extra options of 
step-by-step jet joining. 
   
   
<br/><code>argument</code><strong> useStandardR </strong> (<code>default = <strong>true</strong></code>) : definition of <i>R</i> 
distance between two jets. This switch is only meaningful for 
<code>useFJcore = false</code>; within the <code>fjcore</code> package 
the standard option below is always used. 
<br/><code>argumentoption </code><strong> true</strong> : standard, as described above, 
<i>DeltaR_ij^2 = (y_i - y_j)^2 + (phi_i - phi_j)^2.</i> 
   
<br/><code>argumentoption </code><strong> false</strong> : alternative, 
<i>DeltaR_ij^2 = 2 (cosh(y_i - y_j) - cos(phi_i - phi_j))</i>, 
which corresponds to the rim of the "deformed cone" giving a constant 
invariant mass between the two partons considered (for fixed 
masses and transverse momenta). 
   
   
   
 
<a name="method38"></a>
<p/><strong>bool SlowJet::analyze( const Event& event) &nbsp;</strong> <br/>
performs a jet finding analysis, where 
<br/><code>argument</code><strong> event </strong>  : is an object of the <code>Event</code> class, 
most likely the <code>pythia.event</code> one. 
   
<br/>If the routine returns <code>false</code> the analysis failed, 
but currently this is not foreseen ever to happen. 
   
 
<p/> 
After the analysis has been performed, a few <code>SlowJet</code> 
class methods are available to return the result of the analysis: 
 
<a name="method39"></a>
<p/><strong>int SlowJet::sizeOrig() &nbsp;</strong> <br/>
gives the original number of particles (and thus clusters) that the 
analysis starts with. 
   
 
<a name="method40"></a>
<p/><strong>int SlowJet::sizeJet() &nbsp;</strong> <br/>
gives the number of jets found, with jets numbered 0 through 
<code>sizeJet() - 1</code>, and ordered in terms of decreasing 
transverse momentum values w.r.t. the beam axis, 
   
 
<a name="method41"></a>
<p/><strong>double SlowJet::pT(i) &nbsp;</strong> <br/>
gives the transverse momentum <i>pT</i> of the <i>i</i>'th jet, 
   
 
<a name="method42"></a>
<p/><strong>double SlowJet::y(int i) &nbsp;</strong> <br/>
   
<strong>double SlowJet::phi(int i) &nbsp;</strong> <br/>
gives the rapidity <i>y</i> and azimuthal angle <i>phi</i> 
of the center of the <i>i</i>'th jet (defined by the vector sum 
of constituent four-momenta), 
   
 
<a name="method43"></a>
<p/><strong>Vec4 SlowJet::p(int i) &nbsp;</strong> <br/>
   
<strong>double SlowJet::m(int i) &nbsp;</strong> <br/>
gives a <code>Vec4</code> corresponding to the four-momentum 
sum of the particles assigned to the <i>i</i>'th jet, and 
the invariant mass of this four-vector, 
   
 
<a name="method44"></a>
<p/><strong>int SlowJet::multiplicity(int i) &nbsp;</strong> <br/>
gives the number of particles clustered into the <i>i</i>'th jet, 
   
 
<a name="method45"></a>
<p/><strong>vector&lt;int&gt; SlowJet::constituents(int i) &nbsp;</strong> <br/>
gives a list of the indices of the particles that have been 
clustered into the <i>i</i>'th jet, 
   
 
<a name="method46"></a>
<p/><strong>vector&lt;int&gt; SlowJet::clusConstituents(int i) &nbsp;</strong> <br/>
gives a list of the indices of the particles that have been 
clustered into the <i>i</i>'th cluster, at the current stage of 
the clustering process,  
   
 
<a name="method47"></a>
<p/><strong>int SlowJet::jetAssignment(int i) &nbsp;</strong> <br/>
gives the index of the jet that the particle <i>i</i> of the event 
record belongs to, or -1 if there is no jet containing particle 
<i>i</i>, 
   
 
<a name="method48"></a>
<p/><strong>void SlowJet::removeJet(int i) &nbsp;</strong> <br/>
removes the <i>i</i>'th jet, 
   
 
<a name="method49"></a>
<p/><strong>void SlowJet::list() &nbsp;</strong> <br/>
provides a listing of the basic jet information from above. 
   
 
<p/> 
These are the basic methods. For more sophisticated usage 
it is possible to trace the clustering, one step at a time. 
It requires the native jet finding code, <code>useFJcore = false</code> 
in the constructor. If so, the <code>setup</code> method should be used 
to read in the event and find the initial smallest distance. Each subsequent 
<code>doStep</code> will then do one cluster joining and find the new 
smallest distance. You can therefore interrogate which clusters will be 
joined next before the joining actually is performed. Alternatively you 
can take several steps in one go, or take steps down to a predetermined 
number of jets plus remaining clusters. 
 
<a name="method50"></a>
<p/><strong>bool SlowJet::setup( const Event& event) &nbsp;</strong> <br/>
selects the particles to be analyzed, calculates initial distances, 
and finds the initial smallest distance. 
<br/><code>argument</code><strong> event </strong>  : is an object of the <code>Event</code> class, 
most likely the <code>pythia.event</code> one. 
   
<br/>If the routine returns <code>false</code> the setup failed, 
but currently this is not foreseen ever to happen. 
   
 
<a name="method51"></a>
<p/><strong>bool SlowJet::doStep() &nbsp;</strong> <br/>
do the next step of the clustering. This can either be that two 
clusters are joined to one, or that a cluster is promoted to a jet 
(which is discarded if its <i>pT</i> value is below 
<code>pTjetMin</code>). 
<br/>The routine will only return <code>false</code> if there are no 
clusters left, or if <code>useFJcore = true</code>. 
   
 
<a name="method52"></a>
<p/><strong>bool SlowJet::doNSteps(int nStep) &nbsp;</strong> <br/>
calls the <code>doStep()</code> method <code>nStep</code> times, 
if possible. Will return <code>false</code> if the list of clusters 
is emptied before then. The stored jet information is still perfectly 
fine; it is only the number of steps that is wrong. Will also return 
<code>false</code> if <code>useFJcore = true</code>. 
   
 
<a name="method53"></a>
<p/><strong>bool SlowJet::stopAtN(int nStop) &nbsp;</strong> <br/>
calls the <code>doStep()</code> method until a total of <code>nStop</code> 
jet and cluster objects remain. Will return <code>false</code> if this 
is not possible, specifically if the number of objects already is smaller 
than <code>nStop</code> when the method is called. The stored jet and 
cluster information is still perfectly fine; it only does not have the 
expected multiplicity. Will also return <code>false</code> if 
<code>useFJcore = true</code>. 
   
 
<a name="method54"></a>
<p/><strong>int SlowJet::sizeAll() &nbsp;</strong> <br/>
gives the total current number of jets and clusters. The jets are 
numbered 0 through <code>sizeJet() - 1</code>, while the clusters 
are numbered <code>sizeJet()</code> through <code>sizeAll() - 1</code>. 
(Internally jets and clusters are represented by two separate arrays, 
but are here presented in one flat range.) Note that the jets are ordered 
in decreasing <i>pT</i>, while the clusters are not ordered. 
When <code>useFJcore = true</code> there are no intermediate steps, and 
thus the number of clusters is zero (after jet finding). 
   
 
<p/> 
With this extension, the methods <code>double pT(int i)</code>, 
<code>double y(int i)</code>, <code>double phi(int i)</code>, 
<code>Vec4 p(int i)</code>, <code>double m(int i)</code> and 
<code>int multiplicity(int i)</code> can be used as before. 
Furthermore, <code>list()</code> generalizes 
 
<a name="method55"></a>
<p/><strong>void SlowJet::list(bool listAll = false, ostream& os = cout) &nbsp;</strong> <br/>
provides a listing of the above information. 
<br/><code>argument</code><strong> listAll </strong>  :  lists both jets and clusters if <code>true</code>, 
else only jets. 
   
   
  
<p/> 
Three further methods can be used to check what will happen next. 
 
<a name="method56"></a>
<p/><strong>int SlowJet::iNext() &nbsp;</strong> <br/>
   
<strong>int SlowJet::jNext() &nbsp;</strong> <br/>
   
<strong>double SlowJet::dNext() &nbsp;</strong> <br/>
if the next step is to join two clusters, then the methods give 
the <i>(i,j, d_ij)</i> values, if instead to promote 
a cluster to a jet then <i>(i, -1, d_iB)</i>. 
If no clusters remain then <i>(-1, -1, 0.)</i>. Note that 
the cluster numbers are offset as described above, i.e. they begin at 
<code>sizeJet()</code>, which of course easily could be subtracted off. 
Also note that the jet and cluster lists are (moderately) reshuffled 
in each new step. When <code>useFJcore = true</code> there are no 
intermediate steps, and thus these methods do not return meaningul 
information. 
   
  
<p/> 
Finally, and separately, the <code>SlowJetHook</code> class can be used 
for a more smart selection of which particles to include in the analysis. 
For instance, isolated and/or high-<i>pT</i> muons, electrons and 
photons should presumably be identified separately at an early stage, 
and then not clustered to jets. 
  
<p/> 
Technically, it works like with <?php $filepath = $_GET["filepath"];
echo "<a href='UserHooks.php?filepath=".$filepath."' target='page'>";?>User Hooks</a>. 
That is, PYTHIA contains the base class. You write a derived class. 
In the main program you create an instance of this class, and hand it 
in to <code>SlowJet</code>; in this case already as part of the 
constructor. 
 
<p/> 
The following methods should be defined in your derived class. 
 
<a name="method57"></a>
<p/><strong>SlowJetHook::SlowJetHook() &nbsp;</strong> <br/>
   
<strong>virtual SlowJetHook::~SlowJetHook() &nbsp;</strong> <br/>
the constructor and destructor need not do anything, and if so you 
need not write your own destructor. 
   
 
<a name="method58"></a>
<p/><strong>virtual bool SlowJetHook::include(int iSel, const Event& event, Vec4& pSel, double& mSel) &nbsp;</strong> <br/>
is the main method that you will need to write. It will be called 
once for each final-state particle in an event, subject to the 
value of the <code>select</code> switch in the <code>SlowJet</code> 
constructor. The value <code>select = 2</code> may be convenient 
since then you do not need to remove e.g. neutrinos yourself, but 
use <code>select = 1</code> for full control. The method should then 
return <code>true</code> if you want to see particle included in the 
jet clustering, and <code>false</code> if not. 
<br/><code>argument</code><strong> iSel </strong>  : is the index in the event record of the 
currently studied particle. 
   
<br/><code>argument</code><strong> event </strong>  : is an object of the <code>Event</code> class, 
most likely the <code>pythia.event</code> one, where the currently 
studied particle is found. 
   
<br/><code>argument</code><strong> pSel </strong>  :  is at input the four-momentum of the 
currently studied particle. You can change the values, e.g. to take 
into account energy smearing in the detector, to define the initial 
cluster value, without corrupting the event record itself. 
   
<br/><code>argument</code><strong> mSel </strong>  :  is at input the mass of the currently studied 
particle. You can change the value, e.g. to take into account 
particle misidentification, to define the initial cluster value, 
without corrupting the event record itself. Note that the changes of 
<code>pSel</code> and <code>mSel</code> must be coordinated such that 
<i>E^2 - p^2 = m^2</i> holds. 
   
   
 
<p/> 
It is also possible to define further methods of your own. 
One such could e.g. be called directly in the main program before the 
<code>analyze</code> method is called, to identify and bookkeep 
some event properties you may not want to reanalyze for each 
individual particle. 
 
</body>
</html>
 
<!-- Copyright (C) 2014 Torbjorn Sjostrand --> 
