<template>
    <div class="rich-editor">
        <!-- Toolbar -->
        <div class="rich-editor-toolbar d-flex flex-wrap align-items-center gap-1 p-2 border-bottom">

            <!-- Text style -->
            <button type="button" class="editor-btn" :class="{ active: editor?.isActive('bold') }" @click="editor?.chain().focus().toggleBold().run()" title="Bold">
                <i class="bi bi-type-bold"></i>
            </button>
            <button type="button" class="editor-btn" :class="{ active: editor?.isActive('italic') }" @click="editor?.chain().focus().toggleItalic().run()" title="Italic">
                <i class="bi bi-type-italic"></i>
            </button>
            <button type="button" class="editor-btn" :class="{ active: editor?.isActive('underline') }" @click="editor?.chain().focus().toggleUnderline().run()" title="Underline">
                <i class="bi bi-type-underline"></i>
            </button>
            <button type="button" class="editor-btn" :class="{ active: editor?.isActive('strike') }" @click="editor?.chain().focus().toggleStrike().run()" title="Strikethrough">
                <i class="bi bi-type-strikethrough"></i>
            </button>

            <div class="editor-divider"></div>

            <!-- Text color -->
            <div class="editor-color-wrap" title="Text Color">
                <i class="bi bi-fonts"></i>
                <input
                    type="color"
                    :value="currentTextColor"
                    @input="(e) => editor?.chain().focus().setColor((e.target as HTMLInputElement).value).run()"
                />
            </div>

            <!-- Highlight -->
            <div class="editor-color-wrap" title="Highlight Color">
                <i class="bi bi-highlighter"></i>
                <input
                    type="color"
                    value="#fef08a"
                    @input="(e) => editor?.chain().focus().toggleHighlight({ color: (e.target as HTMLInputElement).value }).run()"
                />
            </div>

            <!-- Clear formatting -->
            <button type="button" class="editor-btn" @click="clearFormatting" title="Clear Color & Highlight">
                <i class="bi bi-slash-circle"></i>
            </button>

            <div class="editor-divider"></div>

            <!-- Headings -->
            <button type="button" class="editor-btn" :class="{ active: editor?.isActive('heading', { level: 1 }) }" @click="editor?.chain().focus().toggleHeading({ level: 1 }).run()" title="Heading 1">
                H1
            </button>
            <button type="button" class="editor-btn" :class="{ active: editor?.isActive('heading', { level: 2 }) }" @click="editor?.chain().focus().toggleHeading({ level: 2 }).run()" title="Heading 2">
                H2
            </button>
            <button type="button" class="editor-btn" :class="{ active: editor?.isActive('heading', { level: 3 }) }" @click="editor?.chain().focus().toggleHeading({ level: 3 }).run()" title="Heading 3">
                H3
            </button>

            <div class="editor-divider"></div>

            <!-- Lists -->
            <button type="button" class="editor-btn" :class="{ active: editor?.isActive('bulletList') }" @click="editor?.chain().focus().toggleBulletList().run()" title="Bullet List">
                <i class="bi bi-list-ul"></i>
            </button>
            <button type="button" class="editor-btn" :class="{ active: editor?.isActive('orderedList') }" @click="editor?.chain().focus().toggleOrderedList().run()" title="Numbered List">
                <i class="bi bi-list-ol"></i>
            </button>

            <div class="editor-divider"></div>

            <!-- Blocks -->
            <button type="button" class="editor-btn" :class="{ active: editor?.isActive('blockquote') }" @click="editor?.chain().focus().toggleBlockquote().run()" title="Blockquote">
                <i class="bi bi-blockquote-left"></i>
            </button>
            <button type="button" class="editor-btn" :class="{ active: editor?.isActive('codeBlock') }" @click="editor?.chain().focus().toggleCodeBlock().run()" title="Code Block">
                <i class="bi bi-code-slash"></i>
            </button>

            <div class="editor-divider"></div>

            <!-- Link -->
            <button type="button" class="editor-btn" :class="{ active: editor?.isActive('link') }" @click="setLink" title="Insert Link">
                <i class="bi bi-link-45deg"></i>
            </button>
            <button type="button" class="editor-btn" @click="editor?.chain().focus().unsetLink().run()" :disabled="!editor?.isActive('link')" title="Remove Link">
                <i class="bi bi-link"></i>
            </button>

            <!-- Image -->
            <button type="button" class="editor-btn" @click="insertImage" title="Insert Image">
                <i class="bi bi-image"></i>
            </button>

            <div class="editor-divider"></div>

            <!-- Undo / Redo -->
            <button type="button" class="editor-btn" @click="editor?.chain().focus().undo().run()" :disabled="!editor?.can().undo()" title="Undo">
                <i class="bi bi-arrow-counterclockwise"></i>
            </button>
            <button type="button" class="editor-btn" @click="editor?.chain().focus().redo().run()" :disabled="!editor?.can().redo()" title="Redo">
                <i class="bi bi-arrow-clockwise"></i>
            </button>

        </div>

        <!-- Editor area -->
        <EditorContent :editor="editor" class="rich-editor-content" />
    </div>
</template>

<script setup lang="ts">
import { computed, watch, onBeforeUnmount } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import { TextStyle } from '@tiptap/extension-text-style';
import Color from '@tiptap/extension-color';
import Highlight from '@tiptap/extension-highlight';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';

const props = defineProps<{
    modelValue: string;
    placeholder?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit,
        Underline,
        TextStyle,
        Color,
        Highlight.configure({ multicolor: true }),
        Link.configure({
            openOnClick: false,
            HTMLAttributes: { class: 'editor-link' },
        }),
        Image.configure({
            inline: false,
            allowBase64: true,
        }),
    ],
    editorProps: {
        attributes: { class: 'rich-editor-area' },
    },
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

// Current text color for the color picker
const currentTextColor = computed(() =>
    editor.value?.getAttributes('textStyle').color ?? '#000000'
);

const clearFormatting = () => {
    editor.value?.chain().focus().unsetColor().unsetHighlight().run();
};

const setLink = () => {
    const prev = editor.value?.getAttributes('link').href ?? '';
    const url  = window.prompt('Enter URL', prev);
    if (url === null) return;
    if (url === '') {
        editor.value?.chain().focus().unsetLink().run();
        return;
    }
    editor.value?.chain().focus().setLink({ href: url, target: '_blank' }).run();
};

const insertImage = () => {
    const url = window.prompt('Enter image URL');
    if (url) editor.value?.chain().focus().setImage({ src: url }).run();
};

watch(() => props.modelValue, (val) => {
    if (editor.value && editor.value.getHTML() !== val) {
        editor.value.commands.setContent(val);
    }
});

onBeforeUnmount(() => editor.value?.destroy());
</script>