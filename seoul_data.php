<DOCTYPEhtml>
<html>
    <head>
        <title> OPEN DB </title>

        <style>
            h4:hover {
                color: red;
            }

            h3 {
                color: dodgerblue;
            }

            th:hover {
                background: red;
                color: white;
            }
        </style>

        <h4>Open DB</h4>
        <h3>2017301009 MJ KIM</h3>


        <?php

        $database='seoul_bike';


        $link = mysql_connect("localhost","kimmije1009","mskimmije1009M") or die("DB Connection Failed !!!");
        mysql_select_db("kimmije1009", $link) or die("DB use fail !!!");
        $q = "SELECT column_name from information_schema.columns where table_schema = 'kimmije1009' and table_name = '$database'";
        $res = mysql_query($q, $link) or die("select fail!");
        $num=0;
        $attribute = array('a');
        while($row=mysql_fetch_row($res)) {
            foreach($row as $k=>$v){
                $attribute[$num]=$v;
                $num++;
            }
        }
        $num=0;

        $user_ipadd=$_SERVER['REMOTE_ADDR'];
        echo "현재 당신의 아이피 :".$user_ipadd;

        ?>

        <table border=1>
            <?php
            $fields = mysql_list_fields('kimmije1009',$database,$link);
            $ncols = mysql_num_fields($fields);
            for ($i=0; $i<$ncols; $i++)
                echo "<th bgcolor='yellow'><font color = 'green'>".mysql_field_name($fields, $i)."</font></th>";
            ?>
        </table>


        <form name='my_form' action="seoul_data.php" method="GET">
            <script>
                var javascript_att = new Array();
                var javascript_att = <?php echo json_encode($attribute) ?> ;

                for (var i = 0; i < "<?=$ncols ?>"; i++) {
                    document.write("<input type='checkbox' name='mycheck[]' id = 'mycheck' value ='" + javascript_att[i] + "'><label>" + javascript_att[i] + "</label>");
                }

                document.write("<br>");
                document.write("<input type='button' onclick='check_all();' value='모두 선택' />");
                document.write("<input type='button' onclick='uncheck_all();' value='모두 해제' />");
                document.write("<input type='button' onclick='count_check();' value='체크 수 확인' />");

                document.write("<br>");
                document.write("<input type=text name='myuser' placeholder=\"ID\">");

                document.write("<input type=text name='mytext' placeholder=\"값 입력:은평구\">");
                document.write("<input type='submit' onclick='search_fun();' value='Search'>");
            </script>
        </form>


        <script>
            function check_all() {
                for(i=0; i < my_form.mycheck.length; i++) {
                    my_form.mycheck[i].checked = true;
                }
            }
            function uncheck_all() {
                for(i=0; i < my_form.mycheck.length; i++) {
                    my_form.mycheck[i].checked = false;
                }
            }
            function count_check() {
                var cnt = 0;
                for(i=0; i < my_form.mycheck.length; i++) {
                    if(my_form.mycheck[i].checked) cnt++;
                }
                alert(cnt);
            }

            function search_fun(){
                var out = new Array(); cnt1=0;
                var out_num=0;
                for (i=0; i<my_form.mycheck.length; i++){
                  if(my_form.mycheck[i].checked) {
                      cnt1++;
                      out[out_num] = my_form.mycheck[i].value;
                      out_num++;
                  }
                }
                var st = out.join(',');

//                alert(st);
                /////////////////////////////////////
                var out2 = my_form.mytext.value;
//                alert(out2);
                /////////////////////////////////////

            }//search_fun()

        </script>


    </head>
    <body bgcolor="lightblue">
        <?php

        $user = $_GET['myuser'];


        //echo "검색 : ".$_GET['mytext']."<br>";
        $search_text = $_GET['mytext'];
        $search_text_solve = $_GET['mytext'];
        $ip_search_text = $_GET['mytext'];
        $search_text = "'%".$search_text."%'";
//        echo "검색 : ".$search_text."<br>";

        $id_arr = array('a');
        $id_num=0;
        $id_sum=0;

        $check = $_GET['mycheck'];

        $addText = $_REQUEST[mycheck];
        $addText = implode(',',$addText);
        //echo $addText;
        $id_explode = explode( ',', $addText );
        $explode_count = count($id_explode);

//        for($i=0;$i<$explode_count;$i++){
//            echo $id_explode[$i];
//            echo "<br />";
//        }

        $sql_string = array('a');
        $ssn = 0;  //sql_string_num
        for($i=0;$i<$explode_count;$i++){
            $sql_string[$i] = $id_explode[$i]." like ".$search_text;
//            echo $sql_string[$i];
//            echo "<br />";
        }

        $sql_all_string = implode(" or ",$sql_string);
//      echo $sql_all_string; // this is final 최종으로 들어갈 쿼리문 query 문!!!!

        //select 사용자지정 쿼리문
        $sql = mysql_query("SELECT *  FROM `$database` WHERE $sql_all_string", $link) or die("select fail!");


        // insert 쿼리를 넣는다 어디에 ? ip_search!에!!
        //echo "현재 당신의 아이피 :".$user_ipadd;
        $string_user_ipadd = "'".$user_ipadd."'";
        $search_text ="'".$ip_search_text."'";

        $nn = $user;
        $ns = "무명";
        $ns = "'".$ns."'";

        $user = "'".$user."'";

//        echo $string_user_ipadd;
//        echo $search_text;
//        echo $user;
        if(($nn!='')&&($search_text_solve!='')){
            $ip_insert_sql =  mysql_query("INSERT INTO ip_search (user,user_ip,content) VALUES ($user,$string_user_ipadd,$search_text);", $link) or die("insert ip_search fail!");

        }
        else if(($nn=='')&&($search_text_solve!='')){
        $ip_insert_sql =  mysql_query("INSERT INTO ip_search (user,user_ip,content) VALUES ($ns,$string_user_ipadd,$search_text);", $link) or die("insert ip_search fail!");
        }

        echo "<br>최근에 입력한 유저와 기록들 <br>";
        //유저가 넣은 값들의 테이블
        $user_sql = mysql_query("SELECT *  FROM `ip_search` order by no desc limit 5", $link) or die("select fail!");
        $user_fields = mysql_list_fields('kimmije1009','ip_search',$link);
        $user_ncols = mysql_num_fields($user_fields);

        echo "<table border = 1 bgcolor = 'white'>";
        for ($i=0; $i<$user_ncols; $i++)
                echo "<th bgcolor='gold'>".mysql_field_name($user_fields, $i)."</th>";

        while( $n=mysql_fetch_row($user_sql) ) {
            echo "<tr>";
            foreach($n as $k=>$v) echo "<td align='center'>".$v."</td>";
            echo "</tr>";
        }
        echo "</table>";


        if($search_text_solve!=''){
            //TOP 5 출력 하기
            $top =  mysql_query("INSERT INTO content_count (content , count) VALUES ($search_text,1) ON DUPLICATE KEY UPDATE count=count+1;", $link) or die("insert content_count fail!");

        }

        echo "<br> 인기검색어 5종<br>";
        $top_sql = mysql_query("SELECT *  FROM `content_count`  order by count desc limit 5", $link) or die("select fail!");
        $top_fields = mysql_list_fields('kimmije1009','content_count',$link);
        $top_ncols = mysql_num_fields($top_fields);

        echo "<table border = 1 bgcolor = 'white'>";
        for ($i=0; $i<$top_ncols; $i++)
                echo "<th bgcolor='gold'>".mysql_field_name($top_fields, $i)."</th>";

        while( $n=mysql_fetch_row($top_sql) ) {
            echo "<tr>";
            foreach($n as $k=>$v) echo "<td align='center'>".$v."</td>";
            echo "</tr>";
        }
        echo "</table>";

        $search_found=0;

        echo "<br> 검색결과<br>";
        //데이터 출력 테이블
        echo "<table border = 1 bgcolor = 'white'>";
        for ($i=0; $i<$ncols; $i++)
                echo "<th bgcolor='gold'>".mysql_field_name($fields, $i)."</th>";

        while( $n=mysql_fetch_row($sql) ) {
            echo "<tr>";
            foreach($n as $k=>$v) echo "<td align='center'>".$v."</td>";
            echo "</tr>";
            $search_found++;
        }
        echo "<tr><th>Found</th><th>";
        echo $search_found."</th>";
        echo "<td colspan=".$ncols.">End of Search</td></tr>";
        $search_found=0;

        echo "</table>";



        ?>

    </body>
</html>
