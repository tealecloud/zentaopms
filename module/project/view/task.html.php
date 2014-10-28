<?php
/**
 * The task view file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2013 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id: task.html.php 4894 2013-06-25 01:28:39Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/treeview.html.php';?>
<?php include '../../common/view/datatable.html.php';?>
<?php include './taskheader.html.php';?>
<?php js::set('moduleID', $moduleID);?>
<?php js::set('productID', $productID);?>
<?php js::set('browseType', $browseType);?>

<div class='side' id='taskTree'>
  <a class='side-handle' data-id='projectTree'><i class='icon-caret-left'></i></a>
  <div class='side-body'>
    <div class='panel panel-sm'>
      <div class='panel-heading nobr'>
        <?php echo html::icon($lang->icons['project']);?> <strong><?php echo $project->name;?></strong>
      </div>
      <div class='panel-body'>
        <?php echo $moduleTree;?>
        <div class='text-right'>
          <?php common::printLink('project', 'edit',    "projectID=$projectID", $lang->edit);?>
          <?php common::printLink('project', 'delete',  "projectID=$projectID&confirm=no", $lang->delete, 'hiddenwin');?>
          <?php common::printLink('tree', 'browsetask', "rootID=$projectID&productID=0", $lang->tree->manage);?>
          <?php common::printLink('tree', 'fix',        "root=$projectID&type=task", $lang->tree->fix, 'hiddenwin');?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class='main'>
  <form method='post' id='projectTaskForm'>
    <table class='table table-condensed table-hover table-striped tablesorter table-fixed datatable' id='taskList' data-checkable='true' data-fixed-left-width='450' data-fixed-right-width='150'>
      <?php $vars = "projectID=$project->id&status=$status&parma=$param&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage"; ?>
      <thead>
        <tr>
          <th data-width='80px'>    <?php common::printOrderLink('id',           $orderBy, $vars, $lang->idAB);?></th>
          <th data-width='80px'>   <?php common::printOrderLink('pri',          $orderBy, $vars, $lang->priAB);?></th>
          <th data-width='auto'>   <?php common::printOrderLink('name',         $orderBy, $vars, $lang->task->name);?></th>
          <th data-width='80px' data-flex='true'><?php common::printOrderLink('status',       $orderBy, $vars, $lang->statusAB);?></th>
          <th data-width='80px' data-flex='true'>  <?php common::printOrderLink('deadline',     $orderBy, $vars, $lang->task->deadlineAB);?></th>

          <?php if($this->cookie->windowWidth > $this->config->wideSize):?>
          <th data-width='80px' data-flex='true'>   <?php common::printOrderLink('openedDate',   $orderBy, $vars, $lang->task->openedDateAB);?></th>
          <?php endif;?>

          <th data-width='80px' data-flex='true'>  <?php common::printOrderLink('assignedTo',   $orderBy, $vars, $lang->task->assignedToAB);?></th>
          <th data-width='80px' data-flex='true'>  <?php common::printOrderLink('finishedBy',   $orderBy, $vars, $lang->task->finishedByAB);?></th>

          <?php if($this->cookie->windowWidth > $this->config->wideSize):?>
          <th data-width='80px' data-flex='true'>  <?php common::printOrderLink('finishedDate', $orderBy, $vars, $lang->task->finishedDateAB);?></th>
          <?php endif;?>

          <th data-width='80px' data-flex='true'>  <?php common::printOrderLink('estimate',     $orderBy, $vars, $lang->task->estimateAB);?></th>
          <th data-width='80px' data-flex='true'>  <?php common::printOrderLink('consumed',     $orderBy, $vars, $lang->task->consumedAB);?></th>
          <th data-width='80px' data-flex='true'>  <?php common::printOrderLink('left',         $orderBy, $vars, $lang->task->leftAB);?></th>
          <?php if($project->type == 'sprint') print '<th data-width=\'80px\' data-flex=\'true\'>' and common::printOrderLink('story', $orderBy, $vars, $lang->task->story) and print '</th>';?>
          <th data-width='150px'><?php echo $lang->actions;?></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($tasks as $task):?>
      <?php $class = $task->assignedTo == $app->user->account ? 'style=color:red' : ''; ?>
      <tr class='text-center' data-id='<?php echo $task->id?>'>
        <td>
          <input class='checkbox-row' type='checkbox' name='taskIDList[]'  value='<?php echo $task->id;?>'/> 
          <?php if(!common::printLink('task', 'view', "task=$task->id", sprintf('%03d', $task->id))) printf('%03d', $task->id);?>
        </td>
        <td><span class='<?php echo 'pri' . zget($lang->task->priList, $task->pri, $task->pri)?>'><?php echo zget($lang->task->priList, $task->pri, $task->pri);?></span></td>
        <td class='text-left' title="<?php echo $task->name?>">
          <?php 
          if(!common::printLink('task', 'view', "task=$task->id", $task->name)) echo $task->name;
          if($task->fromBug) echo html::a($this->createLink('bug', 'view', "id=$task->fromBug"), "[BUG#$task->fromBug]", '_blank', "class='bug'");
          ?>
        </td>
        <td class="<?php echo $task->status;?>">
          <?php
          $storyChanged = ($task->storyStatus == 'active' and $task->latestStoryVersion > $task->storyVersion);
          $storyChanged ? print("<span class='warning'>{$lang->story->changed}</span> ") : print($lang->task->statusList[$task->status]);
          ?>
        </td>
        <td class="<?php if(isset($task->delay)) echo 'delayed';?>"><?php if(substr($task->deadline, 0, 4) > 0) echo substr($task->deadline, 5, 6);?></td>

        <?php if($this->cookie->windowWidth > $this->config->wideSize):?>
        <td><?php echo substr($task->openedDate, 5, 6);?></td>
        <?php endif;?>

        <td <?php echo $class;?>><?php echo $task->assignedTo == 'closed' ? 'Closed' : $task->assignedToRealName;?></td>
        <td><?php echo $users[$task->finishedBy];?></td>

        <?php if($this->cookie->windowWidth > $this->config->wideSize):?>
        <td><?php echo substr($task->finishedDate, 5, 6);?></td>
        <?php endif;?>

        <td><?php echo $task->estimate;?></td>
        <td><?php echo $task->consumed;?></td>
        <td><?php echo $task->left;?></td>
        <?php
        if($project->type == 'sprint')
        {
            echo '<td class="text-left" title="' . $task->storyTitle . '">';
            if($task->storyID)
            {
              if(!common::printLink('story', 'view', "storyid=$task->storyID", $task->storyTitle)) print $task->storyTitle;
            }
            echo '</td>';
        }
        ?>
        <td class='text-right'>
        <?php
        common::printIcon('task', 'assignTo', "projectID=$task->project&taskID=$task->id", $task, 'list', '', '', 'iframe', true);
        common::printIcon('task', 'start',    "taskID=$task->id", $task, 'list', '', '', 'iframe', true);

        common::printIcon('task', 'recordEstimate', "taskID=$task->id", $task, 'list', 'time', '', 'iframe', true);
        if($browseType == 'needconfirm')
        {
            $lang->task->confirmStoryChange = $lang->confirm;
            common::printIcon('task', 'confirmStoryChange', "taskid=$task->id", '', 'list', '', 'hiddenwin');
        }
        common::printIcon('task', 'finish', "taskID=$task->id", $task, 'list', '', '', 'iframe', true);
        common::printIcon('task', 'close',    "taskID=$task->id", $task, 'list', '', '', 'iframe', true);
        common::printIcon('task', 'edit',"taskID=$task->id", '', 'list');
        ?>
        </td>
      </tr>
      <?php endforeach;?>
      </tbody>
      <tfoot>
        <tr>
          <?php $columns = ($this->cookie->windowWidth > $this->config->wideSize ? 14 : 12) - ($project->type == 'sprint' ? 0 : 1);?>
          <td colspan='<?php echo $columns;?>'>
            <div class='table-actions clearfix'>
            <?php 
            $canBatchEdit     = common::hasPriv('task', 'batchEdit');
            $canBatchClose    = common::hasPriv('task', 'batchClose') and strtolower($browseType) != 'closedBy';
            $canBatchAssignTo = common::hasPriv('task', 'batchAssignTo');
            if(count($tasks))
            {
                echo "<div class='btn-group'>" . html::selectButton() . '</div>';

                $actionLink = $this->createLink('task', 'batchEdit', "projectID=$projectID");
                $misc       = $canBatchEdit ? "onclick=\"setFormAction('$actionLink')\"" : "disabled='disabled'";

                echo "<div class='btn-group dropup'>";
                echo html::commonButton($lang->edit, $misc);
                echo "<button id='moreAction' type='button' class='btn dropdown-toggle' data-toggle='dropdown'><span class='caret'></span></button>";
                echo "<ul class='dropdown-menu' id='moreActionMenu'>";
                $actionLink = $this->createLink('task', 'batchClose');
                $misc = $canBatchClose ? "onclick=\"setFormAction('$actionLink','hiddenwin')\"" : "class='disabled'";
                echo "<li>" . html::a('#', $lang->close, '', $misc) . "</li>";
                echo "</ul></div>";

                if($canBatchAssignTo)
                {
                    $actionLink = $this->createLink('task', 'batchAssignTo', "projectID=$projectID");
                    echo "<div class='input-group w-150px'>";
                    echo html::select('assignedTo', $memberPairs, '', 'class="form-control chosen"');
                    echo "<span class='input-group-btn'>";
                    echo html::a("javascript:setFormAction(\"$actionLink\")", $lang->task->assign, '', "class='btn'");
                    echo '</span></div>';
                }
            }
            echo "<div class='text'>" . $summary . "</div>";
            ?>
            </div>
            <?php $pager->show();?>
          </td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<?php js::set('replaceID', 'taskList')?>
<script language='javascript'>
$('#project<?php echo $projectID;?>').addClass('active')
$('#<?php echo $browseType;?>Tab').addClass('active')
statusActive = '<?php echo isset($lang->project->statusSelects[$browseType]);?>';
if(statusActive) $('#statusTab').addClass('active')
</script>
<?php include '../../common/view/footer.html.php';?>
