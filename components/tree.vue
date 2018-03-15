<template>
<div class="tree-menu">
    <div class="label-wrapper" @click="toggleChildren">
        <div :style="indent" :class="labelClasses">
            <i v-if="nodes" class="fa" :class="iconClasses"></i> <span class="myfont">{{ label }}</span>
        </div>
    </div>
    <tree-menu v-if="showChildren" v-for="(node, i) in nodes" key="i" :nodes="node.nodes" :label="node.label" :depth="depth + 1">
    </tree-menu>
</div>
</template>

<style>
.tree-menu {
    .label-wrapper {

        padding-bottom: 10px;
        margin-bottom: 10px;
        border-bottom: 1px solid #ccc;
        .has-children {
            cursor: pointer;
        }
    }
}
.myfont{
    font-size:16px;
}
</style>

<script>
export default {
    name: 'tree-menu',
    template: '#tree-menu',
    props: ['nodes', 'label', 'depth'],
    data() {
        return {
            showChildren: true
        }
    },
    computed: {
        iconClasses() {
            return {
                'fa-plus-square-o': !this.showChildren,
                'fa-minus-square-o': this.showChildren
            }
        },
        labelClasses() {
            return {
                'has-children': this.nodes
            }
        },
        indent() {
            return {
                transform: `translate(${this.depth * 50}px)`
            }
        }
    },
    methods: {
        toggleChildren() {
            this.showChildren = !this.showChildren;
        }
    },
    created: function() {
        console.log('tree create');
    }
}
</script>
