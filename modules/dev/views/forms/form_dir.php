<div ng-if='!item.class' ui-tree-handle ng-click="toggle(this);select(this);" 
     ng-class="is_selected(this)">

    <div class="ui-tree-handle-info">
        {{item.items.length}} form{{item.items.length > 1 ? 's' : ''}}
    </div>

    <i ng-show="this.collapsed" class="fa fa-caret-right"></i>
    <i ng-show="!this.collapsed" class="fa fa-caret-down"></i>

    {{item.module}}
    {{item.name}}

</div>
<a ng-if='item.class' target="iframe" 
   href="<?php echo $this->createUrl('update', array('class' => '')); ?>{{item.alias}}&m={{item.mtime}}"
   ui-tree-handle ng-click="select(this)" ng-class="is_selected(this)">
    <i ng-show="!this.collapsed" class="fa fa-file-text-o fa-nm"></i>
    {{item.name}}
</a>
<ol  ui-tree-nodes="" ng-model="item.items">
    <li ng-if="item.name"  ui-tree-node collapsed="true" ng-repeat="(dir, item) in item.items" ng-include="'FormTree'" ui-tree-node>
    </li>
</ol>