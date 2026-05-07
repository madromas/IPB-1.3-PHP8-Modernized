lofi-version от IPB 2.0.0 русифицированная с вожможностью быстрого ответа, login и logout для 1.3.x.

Автор: Vanish.
Форма быстрого ответа, login и logout от dgreen из lofi для 2.0.x
Для 1.3.x. объединил: MFG

На 1.1.x и 1.2.x не проверялась, если кто-то подтвердит, мы обновим тут.

Установка:
а) Создаете директорию, куда это все дело поместите.
б) Закачиваете туда файлы из архива.
в) Заходите там на сервере в index.php для редактирования.
Изменяете под себя настройки:

CODE

define( 'ROOT_PATH', "../" );
define( 'LOFI_NAME', 'txt');
Соответственно такие настройки получаются, если этот lofi-version находится в директории txt, которая находится в директории с форумом.

Производим изменениея в следующих файлах

##################################
Редирект в случае login, logout
/sources/functions.php

Old Code:

 if ( $override != 1 )
 {
                if ( $ibforums->base_url )
                 {
                         $url = $ibforums->base_url.$url;
                 }
                 else
                 {
                         $url = "{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?".$url;
                 }
 }

New Code:

        if ( $override != 1 )
        {
                        if($ibforums->input['lofi']==1)
                        {
                                $url=str_replace("index.php?","/txt/index.php?",$ibforums->base_url);
                        }
                        elseif ( $ibforums->base_url )
                        {
                                $url = $ibforums->base_url.$url;
                        }
                        else
                        {
                                $url = "{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?".$url;
                        }
        }


##################################
Редирект в случае написания мессаг
/sources/lib/post_reply_post.php

Old code:
                //-------------------------------------------------
                // Redirect them back to the topic
                //-------------------------------------------------

                if ($return_to_move == 1)
                {
                        $std->boink_it($class->base_url."act=Mod&CODE=02&f={$class->forum['id']}&t={$this->topic['tid']}");
                }
                else
                {
                        $page = floor( ($this->topic['posts'] + 1) / $ibforums->vars['display_max_posts']);
                        $page = $page * $ibforums->vars['display_max_posts'];
                        $std->boink_it($class->base_url."showtopic={$this->topic['tid']}&st=$page&#entry{$this->post['pid']}");
                }

New code:

                //-------------------------------------------------
                // Redirect them back to the topic
                //-------------------------------------------------

                if($ibforums->input['lofi']==1)
                {
                        $class->base_url=str_replace("index.php?","txt/index.php/",$class->base_url);
                        $std->boink_it($class->base_url."t{$this->topic['tid']}.html");
                }

                if ($return_to_move == 1)
                {
                        $std->boink_it($class->base_url."act=Mod&CODE=02&f={$class->forum['id']}&t={$this->topic['tid']}");
                }
                else
                {
                        $page = floor( ($this->topic['posts'] + 1) / $ibforums->vars['display_max_posts']);
                        $page = $page * $ibforums->vars['display_max_posts'];
                        $std->boink_it($class->base_url."showtopic={$this->topic['tid']}&st=$page&#entry{$this->post['pid']}");
                }

Примичание!
В строчках:
$url=str_replace("index.php?","/txt/index.php?",$ibforums->base_url);
$class->base_url=str_replace("index.php?","txt/index.php/",$class->base_url);

директорию txt заменяем на директорию прописанную index.php файле.

