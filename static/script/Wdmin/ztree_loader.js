
define(['jquery', 'ztree'], function($, ztree) {

    function treeLoader() {

        this.firstClicked = false;

        var self = this;

        this.zTreeObj = false;

        this.setting = {
            view: {
                showLine: true,
                nameIsHTML: true
            },
            edit: {
                enable: true,
                showRemoveBtn: false,
                showRenameBtn: false
            },
            callback: {
                // treeNode点击事件
                onClick: function(event, treeId, treeNode) {},
                // 修改完成前数据处理，包括新建和编辑数据
                beforeRename: function(treeId, treeNode, newName) {
                    var zTree = self._getZtreeObj();
                    if (newName.length === 0) {
                        alert("节点名称不能为空.");
                        setTimeout(function() {
                            zTree.editName(treeNode)
                        }, 10);
                        return false;
                    } else {
                        // aFolderId为0就是新建目录
                        var aFolderId = treeNode.dataId === false ? 0 : treeNode.dataId; // dataId 为 0就是新建|大于0就是修改
                        var aParentId = defaultValue(treeNode.parentId, 0);
                        // [HttpPost] todo
                        $.post("/StudyPoint/MainPointsFolder", {aFolderId: aFolderId, aFolderName: newName, aParentId: aParentId}, function(R) {
                            R = R.toJson();
                            // R == 0 成功 | R > 0 创建成功 | R < 0 失败
                            if (R.s < 0) {
                                if (R.s === -2) {
                                    alert("目录名已存在!");
                                    setTimeout(function() {
                                        zTree.editName(treeNode);
                                    }, 10);
                                } else {
                                    alert("目录操作失败!");
                                }
                            } else if (R.s > 0) {
                                treeNode.dataId = parseInt(R.s);
                            }
                        });
                    }
                    return true;
                },
                onNodeCreated: function(event, treeId, treeNode) {
                    if (!self.firstClicked) {
                        $('#' + treeNode.tId + '_a').click();
                        self.firstClicked = true;
                    }
                }
            }
        };

        // 编辑状态
        this.editing = false;

        // 初始化
        this.init = function(_TreeDiv, requestURI, _callback) {
            Loading.start(_TreeDiv);
            $.get(requestURI, function(zNodes) {
                Loading.finish();
                zNodes = eval("(" + zNodes + ")");
                self.zTreeObj = $.fn.zTree.init($(_TreeDiv), self.setting, zNodes);
                if (typeof _callback !== "undefined")
                    _callback();
            });
        };

        // 添加要点目录
        this.addFolder = function() {
            self.editing = true;
            nodes = self.zTreeObj.getSelectedNodes(),
                    treeNode = nodes[0];
            var parentId = treeNode ? treeNode.dataId : 0;
            var nodeData = {dataId: false, pId: parentId, parentId: parentId, isParent: false, name: "未命名目录"};
            if (treeNode) {
                nodeData.isParent = false;
                treeNode = self.zTreeObj.addNodes(treeNode, nodeData);
            } else {
                nodeData.isParent = true;
                treeNode = self.zTreeObj.addNodes(null, nodeData);
            }
            if (treeNode) {
                self.zTreeObj.editName(treeNode[0]);
            } else {
                alert("叶子节点被锁定，无法增加子节点");
            }
            nodeData = null;
        };

        // 编辑目录
        this.editFolder = function() {
            var zTree = self.zTreeObj;
            nodes = zTree.getSelectedNodes(),
                    treeNode = nodes[0];
            if (nodes.length === 0) {
                alert("请先选择一个节点");
                return;
            }
            zTree.editName(treeNode);
        };

        // 删除要点目录
        this.delFolder = function() {
            var snode = self.getSnode();
            if (snode && confirm('确定要删除选中的要点目录吗?')) {
                var aParentId = defaultValue(snode.parentId, 0);
                $.post("/StudyPoint/MainPointsFolder", {aFolderId: (-1) * snode.dataId, aFolderName: snode.name, aParentId: aParentId}, function(R) {
                    var R = eval("(" + R + ")");
                    if (R.s === "0") {
                        // 成功
                        $('#' + snode.tId).remove();
                    } else {
                        alert('删除失败');
                    }
                });
            }
        };

        // 获取选中的目录节点，只返回一个节点
        this.getSnode = function() {
            var zTree = self.zTreeObj;
            var nodes = zTree.getSelectedNodes();
            if (nodes.length !== 0) {
                for (var i in nodes) {
                    return nodes[i];
                }
            } else
                return false;
        };
    }

    return new treeLoader();
});