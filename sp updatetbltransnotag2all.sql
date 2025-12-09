BEGIN

DECLARE n Int DEFAULT 0;

DECLARE i Int DEFAULT 0;

DECLARE m Int DEFAULT 0;

DECLARE j Int DEFAULT 0;

declare litemid2 int DEFAULT 0;
declare dateTag date;
declare dateTag2 date;
declare dateadddate date;

declare decQty2 decimal(10,2) default 0;
declare strUser varchar(20);
declare strTipe varchar(20);

declare intitem varchar(20) DEFAULT '';
declare strnotrans varchar(20) DEFAULT '';
declare strnotag varchar(20) default '';
declare strop varchar(100) default '';
declare strqc varchar(100) default '';
declare strmesin varchar(20) default '';
declare lnoplan int default 0;
declare lshiftid int default 0;
declare strnotrans2 varchar(20) DEFAULT '';
declare strnotag2 varchar(20) default '';

delete from tbltransnotag2;

SELECT COUNT(notag) into n FROM tbllaptag where adddate2>='2025-08-31' and berat<>0;

SET i=0;
WHILE i<n DO       

        SELECT notag,tgllap,addby,berat,notrans,itemid,adddate,operator,qc,kodemesin,noplan,shiftid
        into strnotag,dateTag2,strUser,decQty2,strnotrans,litemid2,dateadddate,strop,strqc,strmesin
        ,lnoplan,lshiftid FROM tbllaptag 
        where adddate2>='2025-08-31' and berat<>0 LIMIT i,1;

        if strnotag<>'' then
            if lnoplan=0 then
                call updatetbltransnotag2 (dateTag2,strnotag,strnotrans,'buy','','',dateTag2,
            dateadddate,struser,'add',strop,strqc,strmesin,litemid2,decQty2,lnoplan,lshiftid); 
            else
                call updatetbltransnotag2 (dateTag2,strnotag,strnotrans,'blowing','','',dateTag2,
            dateadddate,struser,'add',strop,strqc,strmesin,litemid2,decQty2,lnoplan,lshiftid); 
            end if;
        end if;      
    SET i = i + 1;
END WHILE;

select count(x.notag) into n from tbllaptagp x inner join tbllaptag y on x.notagblowing=y.notag where x.tgllap>='2025-08-31' and y.tgllap>='2025-08-31' 
and x.berat<>0 and y.berat<>0 ;

SET i=0;
WHILE i<n DO 
        SELECT x.notag,x.tgllap,x.addby,x.berat,x.notrans,x.itemid,x.adddate,x.operator,x.qc,x.kodemesin,y.noplan,x.shiftid,y.notag,y.notrans,y.tgllap 
        into strnotag,dateTag,strUser,decQty2,strnotrans,litemid2,dateadddate,strop,strqc,strmesin,lnoplan,lshiftid,strnotag2,strnotrans2,datetag2
        FROM tbllaptagp x inner join tbllaptag y on x.notagblowing=y.notag where x.tgllap>='2025-08-31' and y.tgllap>='2025-08-31'
        and x.berat<>0 and y.berat<>0 LIMIT i,1;

        if strnotag<>'' then
            call updatetbltransnotag2 (dateTag,strnotag,strnotrans,'printing',strnotag2,strnotrans2,dateTag2,
            dateadddate,struser,'add',strop,strqc,strmesin,litemid2,decQty2,lnoplan,lshiftid); 
        end if;    
    SET i = i + 1;
END WHILE;

/*sampai sini dulu y*/
select COUNT(notag) into n from tbltagjb where (tipejb='belib' or tipejb='jualb') and berat<>0;

SET i=0;

WHILE i<n DO 

        SELECT notag,tgljb,addby,tipejb,berat,packid,itemid into litemid2,dateTag2,strUser,strTipe,decQty2,lpackid,litemidreal FROM tbltagjb 

        where (tipejb='belib' or tipejb='jualb') and berat<>0 LIMIT i,1;

        set litemid1='';

        set dateTag1=dateTag2;

        set decQty1=decQty2;

        if litemid2<>'' then

            if strTipe='belib' then

                    call recalculateTblTransnotag (litemid1,dateTag1,litemid2,dateTag2,decQty1,decQty2,'in',lpackid,strUser,1,'buy'); 

            elseif strTipe='jualb' then

                    call recalculateTblTransnotag (litemid1,dateTag1,litemid2,dateTag2,decQty1,decQty2,'out',lpackid,strUser,1,'sell'); 

            end if;

        call UpdateSaldoAwalTblTransAllnotag(litemid2,1,dateTag1);  

        update tbltransnotag set itemid2=litemidreal where itemid=litemid2 and periode=dateTag2; 

        end if;

    SET i = i + 1;

END WHILE;



SELECT COUNT(c.notagblowing) into n from tbltagc c where c.berat<>0 and c.notagblowing<>'' and c.adddate2>='2025-08-31';

SET i=0;

WHILE i<n DO 

        SELECT c.notagblowing,c.adddate2,c.addby,'out',c.berat,0,c.itemid into litemid2,dateTag2,strUser,strTipe,decQty2,lpackid,litemidreal FROM 

         tbltagc c where c.berat<>0 and c.notagblowing<>'' and c.adddate2>='2025-08-31' LIMIT i,1;

        set litemid1='';

        set dateTag1=dateTag2;

        set decQty1=decQty2;

        if litemid2<>'' then

            call recalculateTblTransnotag (litemid1,dateTag1,litemid2,dateTag2,decQty1,decQty2,strTipe,lpackid,strUser,1,'cutting');      

            call UpdateSaldoAwalTblTransAllnotag(litemid2,1,dateTag1);  

            update tbltransnotag set itemid2=litemidreal where itemid=litemid2 and periode=dateTag2;     

        end if;

    

    SET i = i + 1;

END WHILE;



SELECT COUNT(notag) into n FROM tblimport where tgltag>='2025-08-31' and berat<>0;

SET i=0;

WHILE i<n DO 

        SELECT notag,tgltag,addby,'in',berat,0,itemid into litemid2,dateTag2,strUser,strTipe,decQty2,lpackid,litemidreal FROM tblimport 

        where tgltag>='2025-08-31' and berat<>0 LIMIT i,1;

        set litemid1='';

        set dateTag1=dateTag2;

        set decQty1=decQty2;

        if litemid2<>'' then

            call recalculateTblTransnotag (litemid1,dateTag1,litemid2,dateTag2,decQty1,decQty2,strTipe,lpackid,strUser,1,'buy'); 

            call UpdateSaldoAwalTblTransAllnotag(litemid2,1,dateTag1); 

            update tbltransnotag set itemid2=litemidreal where itemid=litemid2 and periode=dateTag2; 

        end if;

      

    SET i = i + 1;

END WHILE;



select count(i.notag) into n from tbltagp x inner join tblimport i on x.notagblowing=i.notag where x.adddate2>='2025-08-31' 

and i.tgltag>='2025-08-31' and x.berat<>0;

SET i=0;

WHILE i<n DO 

        SELECT i.notag,x.adddate2,x.addby,'out',i.berat,0,i.itemid into litemid2,dateTag2,strUser,strTipe,decQty2,lpackid,litemidreal 

        FROM tbltagp x inner join tblimport i on x.notagblowing=i.notag where x.adddate2>='2025-08-31' and i.tgltag>='2025-08-31' and x.berat<>0 LIMIT i,1;

        set litemid1='';

        set dateTag1=dateTag2;

        set decQty1=decQty2;

        if litemid2<>'' then

            call recalculateTblTransnotag (litemid1,dateTag1,litemid2,dateTag2,decQty1,decQty2,strTipe,lpackid,strUser,1,'printing'); 

            call UpdateSaldoAwalTblTransAllnotag(litemid2,1,dateTag1);  

            update tbltransnotag set itemid2=litemidreal where itemid=litemid2 and periode=dateTag2;

        end if;

     

    SET i = i + 1;

END WHILE;



SELECT COUNT(notag) into n FROM tbltagadj where tgladj>='2025-08-31' and berat<>0 and tipeadj='blowing';

SET i=0;

WHILE i<n DO 

        SELECT notag,tgltag,addby,'out',berat,0,itemid into litemid2,dateTag2,strUser,strTipe,decQty2,lpackid,litemidreal FROM tbltagadj 

        where tgladj>='2025-08-31' and berat<>0 and tipeadj='blowing' LIMIT i,1;

        set litemid1='';

        set dateTag1=dateTag2;

        set decQty1=decQty2;

        if litemid2<>'' then

            call recalculateTblTransnotag (litemid1,dateTag1,litemid2,dateTag2,decQty1,decQty2,strTipe,lpackid,strUser,1,'adj'); 

            call UpdateSaldoAwalTblTransAllnotag(litemid2,1,dateTag1);  

            update tbltransnotag set itemid2=litemidreal where itemid=litemid2 and periode=dateTag2; 

        end if;

    

    SET i = i + 1;

END WHILE;



select count(i.notag) into n from tbltagp x inner join (select notag,itemid,sum(berat) as berat from tbltagadj where tgladj>='2025-08-31' and tipeadj='blowing'

 group by notag,itemid) i on x.notagblowing=i.notag where x.adddate2>='2025-08-31' and x.berat<>0 ;

SET i=0;

WHILE i<n DO 

        SELECT i.notag,x.adddate2,x.addby,'out',i.berat,0,i.itemid into litemid2,dateTag2,strUser,strTipe,decQty2,lpackid,litemidreal 

        FROM tbltagp x inner join (select notag,itemid,sum(berat) as berat from tbltagadj where tgladj>='2025-08-31' and tipeadj='blowing'

 group by notag,itemid) i on x.notagblowing=i.notag where x.adddate2>='2025-08-31' and x.berat<>0 LIMIT i,1;

        set litemid1='';

        set dateTag1=dateTag2;

        set decQty1=decQty2;

        if litemid2<>'' then

            call recalculateTblTransnotag (litemid1,dateTag1,litemid2,dateTag2,decQty1,decQty2,strTipe,lpackid,strUser,1,'printing'); 

            call UpdateSaldoAwalTblTransAllnotag(litemid2,1,dateTag1); 

            update tbltransnotag set itemid2=litemidreal where itemid=litemid2 and periode=dateTag2;

        end if;      

    SET i = i + 1;

END WHILE;




End
