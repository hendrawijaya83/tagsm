BEGIN

DECLARE n Int DEFAULT 0;

DECLARE i Int DEFAULT 0;

DECLARE m Int DEFAULT 0;

DECLARE j Int DEFAULT 0;

declare litemid2 int DEFAULT 0;
declare dateTag date;
declare dateTag2 date;
declare dateadddate datetime;

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

SELECT COUNT(y.notag) into n FROM tbllaptag x join tbltag y on x.notag=y.notag where x.tgllap>='2025-08-31' and y.berat<>0;

SET i=0;
WHILE i<n DO       

        SELECT y.notag,x.tgllap,y.addby,y.berat,x.notrans,y.itemid,y.adddate,x.operator,x.qc,y.kodemesin,y.noplan,y.shiftid
        into strnotag,dateTag,strUser,decQty2,strnotrans,litemid2,dateadddate,strop,strqc,strmesin
        ,lnoplan,lshiftid FROM tbllaptag x join tbltag y on x.notag=y.notag
        where x.tgllap>='2025-08-31' and y.berat<>0 LIMIT i,1;

        if strnotag<>'' then
            if lnoplan=0 then
                call updatetbltransnotag2 (dateTag,strnotag,strnotrans,'blowing','','',dateTag,
            dateadddate,struser,'add',strop,strqc,strmesin,litemid2,decQty2,lnoplan,lshiftid,lnoplan); 
            else
                call updatetbltransnotag2 (dateTag,strnotag,strnotrans,'blowing','','',dateTag,
            dateadddate,struser,'add',strop,strqc,strmesin,litemid2,decQty2,lnoplan,lshiftid,lnoplan); 
            end if;
        end if;      
    SET i = i + 1;
END WHILE;

select COUNT(notrans) into n from tbltagjb where (tipejb='belib' or tipejb='belip') and berat<>0;
SET i=0;
WHILE i<n DO 

        SELECT notag,tgljb,editby,berat,notrans,itemid,editdate,'','',kodemesin,0,1,tipejb
        into strnotag,dateTag2,strUser,decQty2,strnotrans,litemid2,dateadddate,strop,strqc,strmesin
        ,lnoplan,lshiftid,strtipe
        FROM tbltagjb 
        where (tipejb='belib' or tipejb='belip') and berat<>0 LIMIT i,1;

        if strnotag<>'' then
                call updatetbltransnotag2 (dateTag2,strnotag,strnotrans,strtipe,'','',dateTag2,
            dateadddate,struser,'add',strop,strqc,strmesin,litemid2,decQty2,lnoplan,lshiftid,lnoplan); 
        end if;
    SET i = i + 1;
END WHILE;

select count(xp.notag) into n from tbllaptagp x join tbltagp xp on x.notag=xp.notag left join tbllaptag y on xp.notagblowing=y.notag 
        where x.tgllap>='2025-08-31' and y.tgllap>='2025-08-31' and xp.berat<>0 and y.berat<>0 ;

SET i=0;
WHILE i<n DO 
        SELECT xp.notag,x.tgllap,xp.addby,xp.berat,xp.notrans,xp.itemid,xp.adddate,x.operator,x.qc,xp.kodemesin,xp.noplan,
        xp.shiftid,y.notag,y.notrans,y.tgllap,y.noplan 
        into strnotag,dateTag,strUser,decQty2,strnotrans,litemid2,dateadddate,strop,strqc,strmesin,lnoplan,lshiftid,strnotag2,strnotrans2,datetag2,lnoplan2
        FROM tbllaptagp x join tbltagp xp on x.notag=xp.notag left join tbllaptag y on xp.notagblowing=y.notag 
        where x.tgllap>='2025-08-31' and y.tgllap>='2025-08-31'
        and xp.berat<>0 and y.berat<>0 LIMIT i,1;

        if strnotag<>'' then
            call updatetbltransnotag2 (dateTag,strnotag,strnotrans,'printing',strnotag2,strnotrans2,dateTag2,
            dateadddate,struser,'add',strop,strqc,strmesin,litemid2,decQty2,lnoplan,lshiftid,lnoplan2); 
        end if;    
    SET i = i + 1;
END WHILE;

SELECT COUNT(c.notrans) into n from tbltagc c join tbllaptag b on b.notag=c.notagblowing and c.notagblowing<>'' 
        where b.berat<>0 and c.adddate2>='2025-08-31' and b.tgllap>='2025-08-31' ;
SET i=0;

WHILE i<n DO 

        SELECT c.notag,c.adddate2,c.editby,b.berat,c.notrans,b.itemid,c.editdate,'','','',0,1,'cutting',b.notag,b.notrans,b.tgllap
        into strnotag,dateTag2,strUser,decQty2,strnotrans,litemid2,dateadddate,strop,strqc,strmesin,
        lnoplan,lshiftid,strtipe,strnotag2,strnotrans2,datetag2
        FROM tbltagc c join tbllaptag b on b.notag=c.notagblowing and c.notagblowing<>'' 
        where b.berat<>0 and c.adddate2>='2025-08-31' and b.tgllap>='2025-08-31' limit i,1 ;

        if strnotrans<>'' then
                call updatetbltransnotag2 (dateTag,strnotag,strnotrans,strtipe,strnotag2,strnotrans2,dateTag2,
            dateadddate,struser,'add',strop,strqc,strmesin,litemid2,decQty2,lnoplan,lshiftid,lnoplan); 
        end if;
    SET i = i + 1;

END WHILE;

SELECT COUNT(c.notrans) into n from tbltagc c join tbllaptagp b on b.notag=c.notagprinting and c.notagprinting<>'' 
        where b.berat<>0 and c.adddate2>='2025-08-31' and b.tgllap>='2025-08-31' ;
SET i=0;

WHILE i<n DO 

        SELECT c.notag,c.adddate2,c.editby,b.berat,c.notrans,b.itemid,c.editdate,'','','',1,1,'cutting',b.notag,b.notrans,b.tgllap
        into strnotag,dateTag2,strUser,decQty2,strnotrans,litemid2,dateadddate,strop,strqc,strmesin,
        lnoplan,lshiftid,strtipe,strnotag2,strnotrans2,datetag2
        FROM tbltagc c join tbllaptagp b on b.notag=c.notagprinting and c.notagprinting<>'' 
        where b.berat<>0 and c.adddate2>='2025-08-31' and b.tgllap>='2025-08-31' limit i,1 ;

        if strnotrans<>'' then
                call updatetbltransnotag2 (dateTag,strnotag,strnotrans,strtipe,strnotag2,strnotrans2,dateTag2,
            dateadddate,struser,'add',strop,strqc,strmesin,litemid2,decQty2,lnoplan,lshiftid,lnoplan); 
        end if;
    SET i = i + 1;

END WHILE;

select COUNT(jb.notrans) into n from tbltagjb jb join tbltransnotag2 y on jb.notag=y.notag and left(y.tipetrans,1)=right(jb.tipejb,1)  
        where (jb.tipejb='jualb' or jb.tipejb='jualp') and y.berat<>0 and y.notag<>'' ;
SET i=0;
WHILE i<n DO 
        SELECT jb.notag,jb.tgljb,jb.editby,y.berat,jb.notrans,y.itemid,jb.editdate,'','','',0,1,'',
        y.notrans,y.tgltrans,0,jb.tipejb 
        into strnotag,dateTag,strUser,decQty2,strnotrans,litemid2,dateadddate,strop,strqc,strmesin,lnoplan,lshiftid,strnotag2,
        strnotrans2,datetag2,lnoplan2,strtipe
        FROM  tbltagjb jb join tbltransnotag2 y on jb.notag=y.notag and left(y.tipetrans,1)=right(jb.tipejb,1) 
        where (jb.tipejb='jualb' or jb.tipejb='jualp') and y.berat<>0 and y.notag<>'' limit i,1;

        if strnotag<>'' then
                call updatetbltransnotag2 (dateTag,strnotag,strnotrans,strtipe,strnotag2,strnotrans2,dateTag2,
            dateadddate,struser,'add',strop,strqc,strmesin,litemid2,decQty2,lnoplan,lshiftid,lnoplan); 
        end if;
    SET i = i + 1;
END WHILE;

End





