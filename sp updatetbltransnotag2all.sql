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
declare lnoplan2 int default 0;
declare lshiftid int default 0;
declare strnotrans2 varchar(20) DEFAULT '';
declare strnotag2 varchar(20) default '';

delete from tbltransnotag2;

SELECT COUNT(notag) into n FROM tbllaptag where tgllap>='2025-08-31' and berat<>0;

SET i=0;
WHILE i<n DO       

        SELECT notag,tgllap,addby,berat,notrans,itemid,adddate,operator,qc,kodemesin,noplan,shiftid
        into strnotag,dateTag2,strUser,decQty2,strnotrans,litemid2,dateadddate,strop,strqc,strmesin
        ,lnoplan,lshiftid FROM tbllaptag 
        where tgllap>='2025-08-31' and berat<>0 LIMIT i,1;

        if strnotag<>'' then
            if lnoplan=0 then
                call updatetbltransnotag2 (dateTag2,strnotag,strnotrans,'blowing','','',dateTag2,
            dateadddate,struser,'add',strop,strqc,strmesin,litemid2,decQty2,lnoplan,lshiftid,lnoplan); 
            else
                call updatetbltransnotag2 (dateTag2,strnotag,strnotrans,'blowing','','',dateTag2,
            dateadddate,struser,'add',strop,strqc,strmesin,litemid2,decQty2,lnoplan,lshiftid,lnoplan); 
            end if;
        end if;      
    SET i = i + 1;
END WHILE;

select COUNT(notrans) into n from tbltagjb where (tipejb='belib' or tipejb='belip' or tipejb='belic') and berat<>0;
SET i=0;
WHILE i<n DO 

        SELECT notag,tgljb,editby,berat,notrans,itemid,editdate,'','',kodemesin,0,1,tipejb
        into strnotag,dateTag2,strUser,decQty2,strnotrans,litemid2,dateadddate,strop,strqc,strmesin
        ,lnoplan,lshiftid,strtipe
        FROM tbltagjb 
        where (tipejb='belib' or tipejb='belip' or tipejb='belic') and berat<>0 LIMIT i,1;

        if strnotag<>'' then
                call updatetbltransnotag2 (dateTag2,strnotag,strnotrans,strtipe,'','',dateTag2,
            dateadddate,struser,'add',strop,strqc,strmesin,litemid2,decQty2,lnoplan,lshiftid,lnoplan); 
        end if;
    SET i = i + 1;
END WHILE;

select count(xp.notag) into n from tbllaptagp x right join tbltagp xp on x.notag=xp.notag left join tbllaptag y on xp.notagblowing=y.notag 
        where xp.adddate2>='2025-08-31' and y.tgllap>='2025-08-31' and xp.berat<>0 and y.berat<>0 ;

SET i=0;
WHILE i<n DO 
        SELECT xp.notag,xp.adddate2,xp.addby,xp.berat,xp.notrans,xp.itemid,xp.adddate,x.operator,x.qc,xp.kodemesin,xp.noplan,xp.shiftid,y.notag,y.notrans,y.tgllap,y.noplan 
        into strnotag,dateTag,strUser,decQty2,strnotrans,litemid2,dateadddate,strop,strqc,strmesin,lnoplan,lshiftid,strnotag2,strnotrans2,datetag2,lnoplan2
        FROM tbllaptagp x right join tbltagp xp on x.notag=xp.notag left join tbllaptag y on xp.notagblowing=y.notag 
        where xp.adddate2>='2025-08-31' and y.tgllap>='2025-08-31'
        and xp.berat<>0 and y.berat<>0 LIMIT i,1;

        if strnotag<>'' then
            call updatetbltransnotag2 (dateTag,strnotag,strnotrans,'printing',strnotag2,strnotrans2,dateTag2,
            dateadddate,struser,'add',strop,strqc,strmesin,litemid2,decQty2,lnoplan,lshiftid,lnoplan2); 
        end if;    
    SET i = i + 1;
END WHILE;

select COUNT(notrans) into n from tbltagjb where (tipejb='jualb' or tipejb='jualp' or tipejb='jualc') and berat<>0;
SET i=0;
WHILE i<n DO 
        SELECT xp.notag,xp.tgljb,xp.addby,xp.berat,xp.notrans,xp.itemid,xp.adddate,x.operator,x.qc,xp.kodemesin,xp.noplan,xp.shiftid,y.notag,y.notrans,y.tgllap,y.noplan 
        into strnotag,dateTag,strUser,decQty2,strnotrans,litemid2,dateadddate,strop,strqc,strmesin,lnoplan,lshiftid,strnotag2,strnotrans2,datetag2,lnoplan2
        FROM  tbltagjb xp join tbllaptag y on xp.notagblowing=y.notag 
        where xp.tgljb>='2025-08-31' and y.tgllap>='2025-08-31'
        and xp.berat<>0 and y.berat<>0 LIMIT i,1;

        SELECT notag,tgljb,editby,berat,notrans,itemid,editdate,'','',kodemesin,0,1,tipejb
        into strnotag,dateTag2,strUser,decQty2,strnotrans,litemid2,dateadddate,strop,strqc,strmesin
        ,lnoplan,lshiftid,strtipe
        FROM tbltagjb 
        where (tipejb='jualb' or tipejb='jualp' or tipejb='jualc') and berat<>0 LIMIT i,1;

        if strnotag<>'' then
                call updatetbltransnotag2 (dateTag2,strnotag,strnotrans,strtipe,'','',dateTag2,
            dateadddate,struser,'add',strop,strqc,strmesin,litemid2,decQty2,lnoplan,lshiftid,lnoplan); 
        end if;
    SET i = i + 1;
END WHILE;

/*sampai sini dulu y*/
SELECT COUNT(c.notagblowing) into n from tbltagc c where c.berat<>0 and c.notagblowing<>'' and c.adddate2>='2025-08-31';
SET i=0;

WHILE i<n DO 

        SELECT notag,adddate2,editby,berat,notrans,itemid,editdate,'','',kodemesin,0,1,'cutting',notagblowing
        into strnotag,dateTag2,strUser,decQty2,strnotrans,litemid2,dateadddate,strop,strqc,strmesin,
        ,lnoplan,lshiftid,strtipe
        FROM tbltagc
        where notagblowing<>'' and berat<>0 and adddate2>='2025-08-31' LIMIT i,1;

        if strnotrans<>'' then
                call updatetbltransnotag2 (dateTag2,strnotag,strnotrans,strtipe,'','',dateTag2,
            dateadddate,struser,'add',strop,strqc,strmesin,litemid2,decQty2,lnoplan,lshiftid,lnoplan); 
        end if;
    SET i = i + 1;

END WHILE;

End






