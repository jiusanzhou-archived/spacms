# SPA CMS ʹ���ֲ�

> ����Ŀ������Typecho�Ļ����Ͻ��е���չ������������ʹ����API���������ܡ��������ṩ�Ĺ��Ӳ�������ȫ���㿪����Ҫ�������в����ǽ�����������Դ�룬��������˵����

## 0. ����

### 0. ��Ҫ����

* ���������API�ӿ�
* ��ҳӦ�õ�����
* �ṩ����`jeklly`�ı༭ģ��,���ҳ��������

### 1. ˵��

�������API
��������Mithril SPA

ûʱ����ϸ��Typecho��Դ�룬����û��ʹ�����Ĺ��к���

### 2. �������޸ĵ����ݿ�ṹ

#### 2.1����

* `resources`���������������Դ��ֻ��δ����
* `rls_resources`�����������첽��Դ�Ĺ�ϵ��
* `banners`�������·����������Ҫ��Ϊ����չ��������ͬ���棩����ֻ��δ����

#### 2.2�����ֶ�
* ��`contents`����������`bid`�ֶΣ�����

### 3. API�ӿ�

��ڣ� `index.php/api/`

> �ְ治������

|��ַ|����|����|����|
|:-:|:-:|:-:|:-:|
|`config.json`||��վ������Ϣ(`json`)|��ǰ�˳�ʼ��ʹ��|
|`pages.json`||����ҳ����(`json`)��|��Ⱦ`header`�ϵ�`nav bar`���ְ汾���ں���Ⱦ|
|`pagesfull.json`||����ҳ���б�����������ҳ��ľ�������(`json`)||
|`page/$.json`|ҳ���`slug`|ҳ������(`json`)|��`js`�ڵ�������������Ӻ����ʹ�ã���չʾ��ҳ��|
|`post/$.json`|���µ�`cid`|��������(`html`)||

### 4. �õ����ⲿ��

|����|����|����|·��|
|:-:|:-:|:-:|:-:|
|Twig|http://twig.sensiolabs.org/|����Զ�����Ⱦ����|`plugins/mithril/Twig/`|

### 5. �д����ƵĹ���

* ��������
* �������
* �첽��Դ����
* ��ţ�������˵�Storage�ļ��ϴ�
* �ϴ��ļ��Ĺ���
* ������ļ��ϴ�֮�󣬶�ͼƬ���첽�ļ�������ͨ���ļ�������ɡ�
* �༭����չ
* ǰ�˺������ݵĹ��ˡ�����
* �Ѹ������ݳ�ȡ������ͨ����̨�������
* ���û�д��ĵ�ַ�����ݴ�����Ҫ�����·�����⣩����
* Twig��ģ��洢���ְ汾��ֱ��`new`һ���µ�
* ��̨����SPA

### 6. Դ���޸�

|�ļ�|����|˵��|
|:-:|:-:|:-:|
|admin/custom-fields.php|7|����һ�����ӣ��첽��Դ���������ݵķ���|
|admin/custom-fields-js.php||���js�ļ�|
|var/Widget/Contents/Post/Edit.php|586|��ȡ�Ѿ����ӵ��첽��Դ|
|var/Widget/Contents/Post/Edit.php|586|��ȡ�Ѿ����ӵķ���|

## 1. ʹ��

### 0. ע������

* ����ҳ���е�`slug`Ϊ`index`���ļ������޸����ݣ������޸Ļ���ɾ��`index`
* Mithril��֧��3��urlģʽ�ģ�����������ʵ�ں�̨��Ⱦ��ɣ��õ���`#`ģʽ�����Բ����޸�ǰ�˵�`route.model`
* �ְ汾��`meta`��������û�дӺ�̨��ȡ��ֱ����`index`�ļ��У�ֱ���޸ļ���
* ������Ҫ�ύ���ݵĵط�û�������ݼ�飬�����밴�涨��ʽ�������ݣ��д���û����ʾ
* ������������ݾ�Ϊ`varchar(255)`�����Բ�Ҫ����
* ����ҳ����﷨��ο�`Twig`���ĵ�
* ������������Ҫ����`-`������еĻ�����ѡȡ��ʱ��Ӧ����`posts['a-b']`��������`posts.a-b`
* `slug`��Ҫ��Ϊ����idʹ�ã��ر����ڶ���ҳ��
* ����ҳ���`slug`�Ǻ�`main.js`�е�apiд����Ӧ�����Բ�Ҫ�����޸Ķ���ҳ��`slug`

### 1. ʹ��˵��

�ڶ���ҳ������������Ӧ����������������
* `posts`
* ~~`site`~~
* `theme`

�ڶ���ҳ��ģ���У�`{%  %}`Ϊ����Σ�`{{  }}`Ϊ����ȡֵ

`posts`������Ϊ���з��෢�������£�����������ҳ�档ʾ���������£�
```json
{
    news: {
        count: 5,
        name: "����",
        description: "���ŷ��������",
        slug: "news",
        articles: [
            {   
                title: "��һƪ����"
                author: "John",
                excerp: "ժҪһ��100�֣�����������'...'���ְ汾��֧���Զ���",
                time: "1454312047128",
                banner: {
                    img: "http://...",
                    title: "��ʾ�ڷ����ϵ�����"
                },
                text: "����ȫ��..."
            },
            ...
        ]
    }
}
```

�����ŵ�ҳ������ʾ����
```html
<h2>{{ posts.news.name }}<h2>
<p>һ���У�{{ posts.news.count }}ƪ, posts.news.description</p>
{% for item in posts.news.articles %}
<div class="news-item">
    <div class="left">
        <img src="{{ item.banner.img }}" alt="{{ item.banner.title }}">
    </div>
    <div class="right">
        <h3><a href="#/view/{{ item.slug }}">{{ item.title }}</a></h3>
        <p><span>{{ item.author }}</psan>�����ڣ�{{ item.time | D-M-Y }}</p>
        <p>{{ item.excerp }}</p>
    </div>
</div>
{% endfor %}
```

### 2. �ְ汾���ñ���

#### 2.1 �������·���ҳ

* posts
```json
{
    news: {
        count: 5,
        name: "����",
        description: "���ŷ��������",
        slug: "news",
        articles: [
            {   
                title: "��һƪ����"
                author: "John",
                excerp: "ժҪһ��100�֣�����������'...'���ְ汾��֧���Զ���",
                time: "1454312047128",
                banner: {
                    img: "http://...",
                    title: "��ʾ�ڷ����ϵ�����"
                },
                text: "����ȫ��..."
            },
            ...
        ]
    },
    ...
}
```
* theme
```
{
    url: "usr/themes/Mithril/"
}
```

#### 2.2 INDEX���ҳ

* posts
ͬ��
* headers
```json
[
    {
        slug: "news",
        title: "����"
    },
    ...
]
```
* theme
```
{
    url: "usr/themes/Mithril/"
}
```