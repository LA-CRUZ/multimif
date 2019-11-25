cd ${0%/*}/..

produce()
{
    printf "<?php\n\n" > $2
    classname=${2##*/}
    classname=${classname%.*}
    namespaceLine=$( grep "namespace" $1 )
    namespaceLine=${namespaceLine//"\\"/"\\\\"}
    printf "${namespaceLine/"App"/"App\\Tests"}\n\n" >> $2
    namespace=${namespaceLine//"namespace "/""}
    echo $namespace
    printf "use "${namespace/;/}\\${classname%Test}";\n" >> $2
    printf "use PHPUnit\\Framework\\TestCase;\n\n" >> $2

    printf "class $classname extends TestCase\n{\n" >> $2
        
    functions=$(grep "function" $1)

    for mot in $functions ;do
        if [[ $mot == +(?)\(* ]] ;then
            mot=${mot%%(*}
            printf "    public function test${mot^}()\n    {\n        //TODO\n    }\n\n" >> $2
        fi
    done
    printf "}\n\n?>" >> $2
}

processfile()
{
    file="tests/${1#src/}"
    file=${file/./Test.}
    if [ ! -e $file ] ;then
        echo "touch $file"
        produce $1 $file
    fi
}

processDirectory()
{
    dir="tests/${1##src/}"
    if [ ! -e $dir ] ;then
        echo "mkdir $dir"
        mkdir $dir
    fi

    for truc in $1* ;do
        if [ -d $truc ] ;then
            processDirectory $truc/
        elif [[ $truc == *.php ]] ; then
            processfile $truc
        fi
    done
}

processDirectory src/