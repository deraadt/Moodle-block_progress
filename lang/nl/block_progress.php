<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Progress Bar block Nederlandse vertaling
 *
 * @package    contrib
 * @subpackage block_progress
 * @copyright  2010 linuxpete (at) gmail.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Module names
$string['assign'] = 'Opdracht';
$string['assignment'] = 'Opdracht';
$string['book'] = 'Boek';
$string['certificate'] = 'Certificaat';
$string['chat'] = 'Chat';
$string['choice'] = 'Keuze';
$string['data'] = 'Database';
$string['feedback'] = 'Terugkoppeling';
$string['flashcardtrainer'] = 'Flashcard trainer';
$string['folder'] = 'Folder'; /* is in moodle zo vertaald, bedoeld directory */
$string['forum'] = 'Forum';
$string['glossary'] = 'Glossary';
$string['hotpot'] = 'Hete Aardappels';
$string['imscp'] = 'IMS Content Pakket';
$string['journal'] = 'Logboek';/* gewijzigd  mrt 2011 */
$string['lesson'] = 'Les';
$string['page'] = 'HTML Pagina'; /* gewijzigd  mrt 2011 */
$string['quiz'] = 'Test';
$string['resource'] = 'Bron';
$string['scorm'] = 'SCORM pakket';
$string['url'] = 'URL';
$string['wiki'] = 'Wiki';

// Actions
$string['activity_completion'] = 'activiteit voltooiing';
$string['answered'] = 'beantwoord';
$string['attempted'] = 'gestart';
$string['awarded'] = 'toegekend';
$string['completed'] = 'afgerond';
$string['finished'] = 'klaar';
$string['graded'] = 'gewaardeerd';
$string['marked'] = 'gemarkeerd';
$string['passed'] = 'voorbij';
$string['posted_to'] = 'gepost naar';
$string['responded_to'] = 'beantwoord naar';
$string['submitted'] = 'ingeleverd';
$string['viewed'] = 'gezien';

// Stings for the Config page
$string['config_default_title'] = 'Voortgangs balk';
$string['config_header_action'] = 'Actie';
$string['config_header_expected'] = 'Verwacht voor';
$string['config_header_icon'] = 'Icoon';
$string['config_header_locked'] = 'Gebruik deadline van Activiteit';
$string['config_header_monitored'] = 'Gevolgd';
$string['config_icons'] = 'Gebruik Iconen';
$string['config_monitored'] = 'Gemonitorde Activiteiten en Bronnen'; /* Aangepast */
$string['config_now'] = 'Gebruik'; /* Aangepast */
$string['config_percentage'] = 'Toon percentage aan studenten';
$string['config_title'] = 'Alternatieve titel';

// Help strings
$string['why_set_the_title'] = 'Waarom de Titel wijzigen?';
$string['why_set_the_title_help'] = '
<p>Er kunnen meerdere Voortgangs Blokken bestaan in een cursus. U kunt verschillende Voortgangs Blokken gebruiken om verschillend sets van Activiteiten of Bronnen te volgen. Bijvoorbeeld : u zou de voortgang in opdrachten in één blok en de voortgang van Testen in een ander blok. Om deze reden kunt u de titel instellen op een titel die beter past bij de te volgend onderdelen.</p>
';
$string['why_use_icons'] = 'Waarom u iconen zou willen gebruiken?';
$string['why_use_icons_help'] = '
<p>Het gebruik van vinkjes en kruisjes als iconen in de Voortgangs blok kan het blok beter geschikt maken voor deelnemers met visuele handicaps als kleurenblindheid.</p>
<p>Het kan ook de meer duidelijkheid brengen in de bedoeling van de blokken als de kleuren minder intuitief zijn voor bepaalde studenten, bijvoorbeeld om culturele of persoonlijke redenen</p>
';
$string['why_display_now'] = 'Waarom zou je de NU-indicator willen verbergen of weergeven?'; /* gewijzigd  mrt 2011 */
$string['why_display_now_help'] = '
<p>Niet alle cursussen zijn gericht op het afmaken van taken op een specifieke tijd of datum. Sommige curssusen hebben een open inschrijving welke toestaat dat studenten inschrijven op hun eigen tijd en de cursus volgen in hun eigen tempo</p>
<p>Om de Voortgangsbalk in deze situatie te kunnen gebruiken, zet de "Verwacht voor" datum in de verre toekomst en "Gebruik Nu" instelling op "Nee"</p>
';
$string['what_does_monitored_mean'] = 'Wat betekent gevolgd?';
$string['what_does_monitored_mean_help'] = '
<p>De bedoeling van dit blok is om studenten aan te moedigen effectief om te gaan met hun tijd. Elke student kan met dit blok zijn eigen voortgang in de activiteiten en bronnen volgen.</p>
<p>Op de configuratie pagina ziet u een lijst van alle modules waarvan de voortgang gevolgd kan worden in het Voortgangs blok. De activiteit of bron zal alleen worden gevolgd als het vinkje is geplaatst voor betreffende activiteit/bron. De activiteit of bron zal getoond worden als een klein blokje in het Voortgangs blok.</p>
';
$string['what_locked_means'] = 'Wat betekent de deadline?';
$string['what_locked_means_help'] = '
<p>Een activiteit kan zijn eigen deadline hebben. Als de deadline is ingevuld is het in de Voortgangs module een optie om deze deadline van de activiteit te gebruiken of een andere te zetten voor deze activiteit in het Voortgangs blok.</p>
<p>Om de Voortgangs balk te koppelen aan de deadline van een activiteit moet de deadline van de activiteit zijn ingesteld en ingeschakeld. Als de deadline van de activiteit wordt gebruikt, zullen wijzigingen in de deadline van de activiteit direct worden doorgegeven aan het bijbehorende item in het Voortgangs blok.</p>
<p>Wordt de deadline niet gebruikt dan zullen wijzigingen over en weer geen invloed hebben.</p>
';
$string['what_expected_by_means'] = 'Wat betekent : verwacht voor?';
$string['what_expected_by_means_help'] = '
<p>De <em>Verwacht voor</em> datum/tijd is onafhankelijk van andere datums of tijden in de configuratie van die activiteit of bron. Wanneer u een activiteit of een bron aanmaakt en u bekijkt deze in de configuratie pagina van het Voortgangs blok dan zal het Voortgangs blok een <em>verwachte</em> einddatum schatten aan de hand van datums in de configuratie van de betreffende module. Een standaard datum en tijd worden gebruikt als er geen datum en tijd kunnenn worden geschat. Hierna zal het wijzigen van datums of tijden in actitiviteiten of bronnen geen invloed hebben op het Voortgangs blok en vice-versa.</p>
';
$string['what_actions_can_be_monitored'] = 'Welke lesonderdelen kunnen worden gevolgd?';
$string['what_actions_can_be_monitored_help'] = '
<p>Verschillende lesonderdelen kunnen worden gevolgd. Omdat de verschillende activiteiten en bronnen ook verschillende worden gebruikt is hetgeen wordt gevolgd ook verschillend voor elk lesonderdeel. Bijvoorbeeld, voor opdrachten, wordt het insturen gevolgd; testen worden gevolgd op het starten; forums worden gevolgd op forumposts van studenten; keuzes worden gevolgd op het beantwoorden van de vraag en het bekijken van bronnen wordt gevolgd.</p>
<p>Voor de Opdracht en Test modules, de notie van genomen is gebaseerd op een "Grade door te geven" is vastgelegd voor de kwaliteit item in de cijferlijst. <a href="http://docs.moodle.org/en/Grade_items#Activity-based_grade_items" target="_blank">Meer hulp...</a></p>
';
$string['why_show_precentage'] = 'Waarom tonen een vooruitgang percentage aan studenten?';
$string['why_show_precentage_help'] = '
<p>Het is mogelijk om een totale percentage vooruitgang tonen student.</p>
<p>Dit wordt berekend als het aantal voorwerpen vullen gedeeld door het totale aantal punten in de bar.</p>
<p>De voortgang percentage wordt weergegeven totdat de student muis over een item in de bar.</p>
';

// Other terms
$string['addallcurrentitems'] = 'Voeg alle lesonderdelen/bronnen';
$string['date_format'] = '%a %d %b, %I:%M %p';
$string['mouse_over_prompt'] = 'Laat uw muis over een blok zweven voor informatie.';
$string['no_events_config_message'] = 'Er zijn geen lesonderdelen aanwezig om te laten zien. Creëer lesonderdelen en configureer daarna dit blok.';
$string['no_events_message'] = 'Er zijn geen lesonderdelen om te laten zien. Gebruik config om lesonderdelen toe te voegen.';
$string['no_visible_events_message'] = 'Geen van de geselecteerde lesonderdelen zijn zichtbaar.';
$string['now_indicator'] = 'NU';
$string['pluginname'] = 'Voortgangs balk';
$string['selectitemstobeadded'] = 'Selecteer lesonderdelen/bronnen';
$string['time_expected'] = 'Verwacht';

// Default colours that may have different cultural meanings
$string['attempted_colour'] = '#33CC00';
$string['notAttempted_colour'] = '#FF3300';
$string['futureNotAttempted_colour'] = '#3366FF';

// Overview page strings
$string['lastonline'] = 'Last online';
$string['overview'] = 'Overview of students';
$string['progress'] = 'Progress';
$string['progressbar'] = 'Progress Bar';

// For cabailities
$string['progress:overview'] = 'View course overview of Progress bars for all students';