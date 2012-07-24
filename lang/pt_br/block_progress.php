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
 * Progress Bar block Portuguese-Brasillian language translation
 *
 * @package    contrib
 * @subpackage block_progress
 * @copyright  2010 Maria João Spilker
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Module names
$string['assign'] = 'Tarefa';
$string['assignment'] = 'Tarefa';
$string['book'] = 'Livro';
$string['chat'] = 'Chat';
$string['certificate'] = 'Certificado';
$string['choice'] = 'Escolha';
$string['data'] = 'Database';
$string['feedback'] = 'Feedback';
$string['flashcardtrainer'] = 'Treinador Flashcard';
$string['folder'] = 'Folder';
$string['forum'] = 'Fórum';
$string['glossary'] = 'Glossary';
$string['hotpot'] = 'Batatas Quentes';
$string['imscp'] = 'IMS Content Package';
$string['journal'] = 'Jornal';
$string['lesson'] = 'Lição';
$string['page'] = 'Page';
$string['quiz'] = 'Questionário';
$string['resource'] = 'File';
$string['scorm'] = 'Scorm';
$string['url'] = 'URL';
$string['wiki'] = 'Wiki';

// Actions
$string['activity_completion'] = 'realização de atividade';
$string['answered'] = 'realizada';
$string['attempted'] = 'tentado';
$string['awarded'] = 'adjudicado';
$string['completed'] = 'concluída';
$string['finished'] = 'terminou';
$string['graded'] = 'marcada';
$string['marked'] = 'marcada';
$string['passed'] = 'passou';
$string['posted_to'] = 'postado em';
$string['responded_to'] = 'respondido';
$string['submitted'] = 'submetida';
$string['viewed'] = 'visualizado';

// Stings for the Config page
$string['config_default_title'] = 'Barra de Progresso';
$string['config_header_action'] = 'Ação';
$string['config_header_expected'] = 'Esperado em';
$string['config_header_icon'] = 'Ícone';
$string['config_header_locked'] = 'Bloqueio do prazo';
$string['config_header_monitored'] = 'Monitorado';
$string['config_icons'] = 'Usar ícones';
$string['config_monitored'] = 'Monitored Activities/Resources';
$string['config_now'] = 'Usar';
$string['config_percentage'] = 'Mostrar porcentagem de alunos';
$string['config_title'] = 'Título alternativo';

// Help strings
$string['why_set_the_title'] = 'Por que definir o Título?';
$string['why_set_the_title_help'] = '
<p>Pode haver várias instâncias do bloco Barra de Progresso. Você pode usar diferentes blocos Barra de Progresso para monitorar diferentes conjuntos de atividades ou recursos. Para exemplo, você poderá acompanhar o progresso das tarefas em um bloco e questionários em outro. Por esta razão, você pode substituir o nome padrão e definir um título mais apropriado para cada instância do bloco. </p>
';
$string['why_use_icons'] = 'Por que poderá querer usar ícones?';
$string['why_use_icons_help'] = '
<p>Você pode querer adicionar marca e cruz ("tick and cross") ícones na Barra de Progresso para tornar este bloco visualmente mais acessível para os alunos com deficiência visual.</p>
<p>Pode também clarificar o significado do bloco, se você acredita que as cores não são intuitivas, quer por razões culturais ou pessoais.</p>
';
$string['why_display_now'] = 'Por que você pode querer esconder / mostrar o indicador AGORA';
$string['why_display_now_help'] = '
<p>Not all course are focussed on completion of tasks by specific times. Some courses may have an open-enrollment, allowing students to enrol and complete when they can.</p>
<p>To use the Progess Bar as a tool in such courses, create "Expected by" dates in the far-future and set the "Use NOW" setting to No.</p>
';
$string['what_does_monitored_mean'] = 'O que significa Monitorado?';
$string['what_does_monitored_mean_help'] = '
<p>O objetivo deste bloco é incentivar os alunos a gerir o seu tempo de forma eficaz. Cada aluno pode monitorar seu progresso no que respeita ao completar as atividades e visualizar os recursos que você criou.</p>
<p>Na página de configuração, você verá uma lista de todos os módulos que você criou, que podem ser monitorados pelo bloco Barra de Progresso. O módulo só será monitorado e aparecerá como um pequeno bloco na Barra de Progresso, se a respectiva actividade/recurso for selecionada. </p>
';
$string['what_locked_means'] = 'O que significa bloquear o prazo?';
$string['what_locked_means_help'] = '
<p>Sempre que uma atividade pode, nas suas próprias configurações, ter um prazo, e um prazo limite foi definido, é opcional utilizar o prazo da atividade, ou definir uma data diferente utilizada na Barra de Progresso.</p>
<p>Para bloquear a Barra de Progresso prazo de uma atividade deve ter prazo habilitado e definido. Se o prazo está bloqueado, alterando o prazo de configurações da atividade irá mudar automaticamente a data associada com a atividade na barra de progresso.</p>
<p>Quando para uma atividade não foi definido um prazo, mudando a data e hora nas configurações da Barra de Progresso não afetará o prazo da atividade.</p>
';
$string['what_expected_by_means'] = 'O que significa Esperado em?';
$string['what_expected_by_means_help'] = '
<p>A data e hora <em> Esperado em </em> é independente de qualquer data ou hora na configuração de uma atividade ou recurso. Quando você inicialmente criar uma nova atividade ou recurso e visita a página de configuração da Barra de Progresso, o bloco tentará adivinhar uma adequada <em> Esperado em </em> data e hora baseados nas datas de configuração do módulo (uma data padrão será usada se não houver nenhuma expectativa). Depois disso, todas as alterações a data na Barra de Progresso não terá qualquer efeito sobre a atividade/recurso monitorada e vice-versa.</p>
';
$string['what_actions_can_be_monitored'] = 'Quais as ações podem ser monitoradas?';
$string['what_actions_can_be_monitored_help'] = '
<p>O fato de diferentes atividades e recursos serem utilizados de forma diferente, o que é monitorado para cada módulo varia. Por exemplo, para as Tarefas, a submissão é monitorada; Questionários são monitorados pela tentativa realizada; Fóruns são monitorados para postagens colocadas pelo estudante; a atividade Escolha é monitorada pela seleção de uma alternativa e recursos são monitorados pelo acesso/visualização.</p>
';
$string ['why_show_precentage'] = 'Por que mostrar uma porcentagem de progresso para os alunos?';
$string ['why_show_precentage_help'] = '
<p>É possível mostrar uma percentagem global dos progressos realizados para os alunos.</p>
<p>Este é calculado como o número de itens completar dividido pelo número total de itens na barra.</p>
<p>O progresso percentual parece até que os mouses dos alunos sobre um item na barra.</p>
';

// Other terms
$string['addallcurrentitems'] = 'Adicione todas as atividades/recursos';
$string['date_format'] = '%a %d %b, %I:%M %p';
$string['mouse_over_prompt'] = 'Passe com o mouse para obter informações';
$string['no_events_config_message'] = 'Não existem atividades ou recursos a serem monitorados. Crie primeiro atividades e/ou recursos e retorne depois a este bloco.';
$string['no_events_message'] = 'Sem eventos a serem monitorados. Use a Configuração para adicionar eventos.';
$string['no_visible_events_message'] = 'Nenhum dos eventos selecionados se encontra atualmente visível.';
$string['now_indicator'] = 'AGORA';
$string['pluginname'] = 'Barra de Progresso';
$string['selectitemstobeadded'] = 'Selecione as actividades/recursos';
$string['time_expected'] = 'Esperado';

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